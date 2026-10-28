<?php
/*!
 * Traq
 * Copyright (C) 2009-2026 Jack Polgar
 * Copyright (C) 2012-2026 Traq.io
 * https://github.com/nirix
 * http://traq.io
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq\Controllers;

use Avalon\Http\Request;
use Avalon\Http\Router;
use Avalon\Output\View;
use Avalon\Http\Response;
use Traq\Controllers\AppController;
use Traq\Models\Project;
use Traq\Models\UserRole;
use Traq\Models\Milestone;
use Traq\Models\Component;
use Traq\Models\Ticket;
use Traq\Models\User;
use Traq\Models\Subscription;
use Traq\Models\CustomField;
use Traq\Models\Timeline;
use Traq\Helpers\Pagination;
use Traq\Helpers\RelatedTickets;
use Traq\Helpers\TicketInput;
use Traq\Helpers\TicketManager;
use Traq\Middleware\AuthMiddleware;
use Traq\Models\Attachment;
use Traq\Models\TicketRelationType;
use Traq\Queries\TicketFilterQuery;

/**
 * Ticket controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class TicketController extends AppController
{
    protected $custom_fields = [];

    /**
     * Custom constructor, we need to do extra stuff.
     */
    public function __construct()
    {
        parent::__construct();

        // Set the title and load the helper
        $this->title(l('tickets'));

        // Custom fields
        $this->custom_fields = CustomField::for_project($this->project->id);
        View::set('custom_fields', $this->custom_fields);
    }

    public function index(): Response
    {
        // TODO: remove once the router supporting mapping request methods to different controllers.
        if (Request::method() === 'POST') {
            if (!$this->user->permission($this->project->id, 'create_tickets')) {
                return $this->renderNoPermission();
            }

            return $this->create();
        }

        if (!$this->user->permission($this->project->id, 'view_tickets')) {
            return $this->renderNoPermission();
        }

        if ($this->isJson) {
            return $this->ticketsJson();
        }

        $customFields = $this->project->getCustomFields();

        return $this->render('tickets/index.phtml', [
            'customFields' => $customFields
        ]);
    }

    public function ticketsJson(): Response
    {
        // Sort keys are resolved to SQL (and whitelisted) by TicketFilterQuery.
        [$sortField, $sortDirection] = array_pad(explode('.', (string) $this->project->default_ticket_sorting), 2, 'desc');

        if (Request::get('order_by')) {
            [$sortField, $sortDirection] = array_pad(explode('.', (string) Request::get('order_by')), 2, 'desc');
        }

        $ticketFilterQuery = new TicketFilterQuery(
            projectId: $this->project->id,
            sortField: $sortField,
            sortDirection: $sortDirection,
            queryString: $_SERVER['QUERY_STRING'] ?? ''
        );

        $pagination = new Pagination(
            (isset(Request::$request['page']) ? Request::$request['page'] : 1),
            settings('tickets_per_page'),
            $ticketFilterQuery->getRowCount()
        );

        $tickets = $ticketFilterQuery->getTickets(settings('tickets_per_page'), $pagination->limit);

        array_walk($tickets, function (&$ticket) {
            $ticket = $ticket->toArray();
        });

        return $this->json([
            'page' => (int) ($pagination->total_pages > 0 ? $pagination->page : 1),
            'total_pages' => (int) $pagination->total_pages,
            'tickets' => $tickets,
        ]);
    }

    /**
     *
     * Handles the view ticket page.
     *
     * @param integer $ticket_id
     */
    #[AuthMiddleware(['view_tickets'])]
    public function view(int $ticket_id): Response
    {
        // Fetch the ticket from the database and send it to the view.
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

        // Does ticket exist?
        if (!$ticket) {
            return $this->show404();
        }

        extract($this->getTicketData($ticket));

        // Atom feed
        $this->feeds[] = array(Request::requestUri() . ".atom", l('x_x_history_feed', $this->project->name, $ticket->summary));

        // Set title and send ticket to view
        $this->title($ticket->summary);

        extract($this->getTicketData($ticket));

        if ($this->isJson) {
            return $this->json([
                'ticket' => $ticket,
                'attachments' => array_map(function (Attachment $attachment) {
                    $attachment = $attachment->toArray();
                    unset($attachment['contents']);

                    return $attachment;
                }, $attachments),
                'ticketHistory' => $ticketHistory,
            ]);
        }

        if ($this->isAtom) {
            return $this->render('tickets/view.atom.phtml', [
                'ticket' => $ticket,
                'ticketHistory' => $ticketHistory,
            ]);
        }

        return $this->render('tickets/view.phtml', [
            'ticket' => $ticket,
            'attachments' => $attachments,
            'ticketHistory' => $ticketHistory,
        ]);
    }

    private function getTicketData(Ticket $ticket): array
    {
        // Ticket history
        $ticketHistory = $ticket->history;

        switch (settings('ticket_history_sorting')) {
            case 'oldest_first':
                $ticketHistory->order_by('created_at', 'ASC');
                break;

            case 'newest_first':
                $ticketHistory->order_by('created_at', 'DESC');
                break;
        }

        $ticketHistory = $ticketHistory->exec()->fetchAll();
        $attachments = $ticket->attachments->exec()->fetchAll();

        return [
            'attachments' => $attachments,
            'ticketHistory' => $ticketHistory,
        ];
    }

    #[AuthMiddleware(['ticket_properties_change_related_tickets'])]
    public function relatedTickets(int $ticket_id): Response
    {
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

        if (!$ticket) {
            return $this->show404();
        }

        return $this->json([
            'related_tickets' => $ticket->relatedTickets(true),
            'relation_types' => TicketRelationType::select()->exec()->fetchAll(),
        ]);
    }

    #[AuthMiddleware(['ticket_properties_change_related_tickets'])]
    public function updateRelatedTickets(int $ticket_id): Response
    {
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();
        if (!$ticket) {
            return $this->show404();
        }

        $requestBody = json_decode(Request::body(), true);
        $existingRelatedTickets = $ticket->relatedTickets();

        foreach ($requestBody['related_tickets'] as $index => $relatedTicket) {
            $related = Ticket::findByProjectIdAndTicketId($this->project->id, $relatedTicket['ticket_id']);

            // Use the project from the database, not the client supplied one.
            if ($related && $this->user->permission($related->project_id, 'ticket_properties_change_related_tickets')) {
                $requestBody['related_tickets'][$index]['actual_ticket_id'] = $related->id;
            } else {
                unset($requestBody['related_tickets'][$index]);
            }
        }

        // Get relation types and index by id and inverse id
        $relationTypes = [];
        foreach (TicketRelationType::select()->exec()->fetchAll() as $relationType) {
            $relationTypes[$relationType->id] = $relationType->inverse_type_id;
        }

        foreach ($requestBody['related_tickets'] as $index => $relatedTicket) {
            // Get the existing related ticket
            $existingRelatedTicket = array_filter($existingRelatedTickets, function ($existingRelatedTicket) use ($relatedTicket) {
                return $existingRelatedTicket->ticket_id === (int) $relatedTicket['ticket_id'];
            });

            // If the existing related ticket is found, update it
            if (count($existingRelatedTicket)) {
                $existingRelatedTicket = end($existingRelatedTicket);
                RelatedTickets::update($ticket, $relatedTicket['actual_ticket_id'], $relatedTicket['relation_type_id'], $relationTypes[$relatedTicket['relation_type_id']]);
            } else {
                RelatedTickets::add($ticket, $relatedTicket['actual_ticket_id'], $relatedTicket['relation_type_id'], $relationTypes[$relatedTicket['relation_type_id']]);
            }
        }

        foreach ($existingRelatedTickets as $existingRelatedTicket) {
            $updatedRelatedTickets = array_filter($requestBody['related_tickets'], function ($relatedTicket) use ($existingRelatedTicket) {
                return (int) $relatedTicket['ticket_id'] === $existingRelatedTicket->ticket_id;
            });

            if (count($updatedRelatedTickets) === 0) {
                $related = Ticket::findByProjectIdAndTicketId($existingRelatedTicket->project_id, $existingRelatedTicket->ticket_id);
                RelatedTickets::delete($ticket, $related->id);
            }
        }

        return $this->relatedTickets($ticket_id);
    }

    /**
     * Handles the add vote page.
     *
     * @param integer $ticket_id
     */
    #[AuthMiddleware(['vote_on_tickets'])]
    public function vote(int $ticket_id)
    {
        // Get the ticket
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

        // Don't let the owner vote on their own ticket
        if ($this->user->id == $ticket->user_id) {
            return $this->renderNoPermission();
        }

        // Cast the vote
        if ($ticket->add_vote($this->user->id)) {
            $ticket->save();

            if ($this->isJson) {
                return $this->json([
                    'votes' => $ticket->votes,
                ]);
            }
        }

        if ($this->isJson) {
            return $this->json([
                'error' => l('errors.already_voted'),
            ]);
        }

        return $this->redirectTo($ticket->href());
    }

    /**
     * Handles the voters page.
     *
     * @param integer $ticket_id
     */
    public function voters($ticket_id)
    {
        // Get the ticket
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

        $voters = [];

        // Have there been any votes?
        if (isset($ticket->extra['voted']) and is_array($ticket->extra['voted'])) {
            // Populate the voters array
            foreach ($ticket->extra['voted'] as $voter) {
                $user = User::find($voter);

                if ($user) {
                    $voters[] = $user;
                }
            }
        }

        View::set('voters', $voters);

        if (Request::get('overlay') === 'true') {
            return $this->render('tickets/voters.overlay.phtml');
        }

        return $this->render('tickets/voters.popover.phtml');
    }

    /**
     * Handles the new ticket page.
     */
    #[AuthMiddleware(['create_tickets'])]
    public function newTicket(): Response
    {
        return $this->render('tickets/new.phtml', [
            'customFields' => $this->custom_fields,
            'ticket' => new Ticket(array(
                'severity_id' => 4,
                'priority_id' => 3,
                'status_id'   => 1,
                'type_id'     => $this->project->default_ticket_type_id
            )),
        ]);
    }

    /**
     * Handles the creation of a new ticket.
     */
    #[AuthMiddleware(['create_tickets'])]
    public function create(): Response
    {
        // Set the title
        $this->title(l('new_ticket'));

        // Create a new ticket object
        $ticket = new Ticket(array(
            'severity_id' => 4,
            'priority_id' => 3,
            'status_id'   => 1,
            'type_id'     => $this->project->default_ticket_type_id
        ));

        $input = new TicketInput($this->user, $this->project);
        $ticket->set($input->newTicket());

        $manager = new TicketManager($ticket, $this->user);
        $manager->setCustomFields($this->custom_fields, $input->customFields());

        // Check if the ticket data is valid...
        // if it is, save the ticket to the DB and
        // redirect to the ticket page.
        if (($this->user->group->is_admin || check_ticket_creation_delay($ticket)) && $ticket->is_valid() && $manager->create()) {
            // Set last ticket creation time
            $_SESSION['last_ticket_creation'] = time();

            // Create subscription
            if ($this->user->option('watch_created_tickets')) {
                $sub = new Subscription(array(
                    'type'       => 'ticket',
                    'user_id'    => $this->user->id,
                    'project_id' => $this->project->id,
                    'object_id'  => $ticket->id
                ));
                $sub->save();
            }

            if ($this->isApi) {
                return $this->json([
                    'ticket' => $ticket->__toArray()
                ]);
            } else {
                return $this->redirectTo($ticket->href());
            }
        }

        if ($this->isJson) {
            return $this->json([
                'errors' => $ticket->errors,
            ], 422);
        }

        return $this->render('tickets/new.phtml', [
            'ticket' => $ticket
        ]);
    }

    /**
     * Handles the updating of the ticket.
     */
    #[AuthMiddleware(['update_tickets'])]
    public function update(int $ticket_id)
    {
        $ticket = Ticket::findByProjectIdAndTicketId($this->project->id, $ticket_id);

        if (!$ticket) {
            return $this->show404();
        }

        $this->title($ticket->summary);
        $this->title(l('update_ticket'));

        $manager = new TicketManager($ticket, $this->user);

        if ($manager->updateFromRequest(new TicketInput($this->user, $this->project), $this->custom_fields)) {
            if ($this->isJson) {
                return $this->json(['ticket' => $ticket]);
            }

            return $this->redirectTo($ticket->href());
        }

        if ($this->isJson) {
            return $this->json(['errors' => $ticket->errors], 422);
        }

        $this->set($this->getTicketData($ticket));

        return $this->render('tickets/view.phtml', ['ticket' => $ticket]);
    }

    /**
     *
     * Handles the editing of the ticket description.
     */
    #[AuthMiddleware(['edit_ticket_description'])]
    public function edit(int $ticket_id)
    {
        $ticket = Ticket::findByProjectIdAndTicketId($this->project->id, $ticket_id);

        if (!$ticket) {
            return $this->show404();
        }

        $this->title($ticket->summary);
        $this->title(l('edit_ticket'));

        // Has the form been submitted?
        if (Request::method() == 'POST') {
            // Set the ticket body
            $ticket->body = Request::$post['body'];

            // Save and redirect
            if ($ticket->save()) {
                Request::redirect(Request::base($ticket->href()));
            }
        }

        View::set('ticket', $ticket);

        return $this->render('tickets/edit.phtml');
    }

    /**
     * Move ticket.
     *
     * @param integer $ticket_id
     */
    #[AuthMiddleware(['move_tickets'])]
    public function move(int $ticket_id)
    {
        $ticket = Ticket::findByProjectIdAndTicketId($this->project->id, $ticket_id);

        if (!$ticket) {
            return $this->show404();
        }

        // Only projects the user can create tickets in are valid destinations
        $projectOptions = [];
        foreach ($this->projects as $project) {
            if ($this->user->permission($project->id, 'create_tickets')) {
                $projectOptions[] = ['label' => $project->name, 'value' => $project->id];
            }
        }
        View::set('projectOptions', $projectOptions);

        // Steps 2 and 3 (and API moves) change state, so they must be POSTs
        $step = (int) Request::get('step', 1);
        $isMove = $step === 3 || $this->isApi;
        if (Request::method() !== 'POST') {
            if ($this->isApi) {
                return $this->json(['error' => 'POST required'], 400);
            }

            $step = 1;
            $isMove = false;
        }

        $next_step = 2;

        if ($step === 2 || $isMove) {
            $new_project = $this->moveDestination((int) Request::get('project_id'));

            if (!$new_project) {
                return $this->renderNoPermission();
            }

            View::set('new_project', $new_project);
        }

        // Step 2
        if ($step === 2 && !$isMove) {
            $next_step = 3;
        }
        // Step 3
        elseif ($isMove) {
            $next_step = 2;

            // Nothing to do if it's already in that project
            if ((int) $new_project->id === (int) $this->project->id) {
                return $this->redirectTo($ticket->href());
            }

            $milestoneId = (int) Request::get('milestone_id', 0);
            $versionId = (int) Request::get('version_id', 0);
            $componentId = (int) Request::get('component_id', 0);
            $assignedToId = (int) Request::get('assigned_to_id', 0);

            // Milestone, version and component must belong to the new project
            // and the assignee must be a member of it.
            foreach ([$milestoneId, $versionId] as $id) {
                $milestone = $id > 0 ? Milestone::find($id) : null;
                if ($id > 0 && (!$milestone || (int) $milestone->project_id !== (int) $new_project->id)) {
                    return $this->renderNoPermission();
                }
            }

            if ($componentId > 0) {
                $component = Component::find($componentId);
                if (!$component || (int) $component->project_id !== (int) $new_project->id) {
                    return $this->renderNoPermission();
                }
            }

            if (
                $assignedToId > 0
                && !UserRole::select('id')->where('project_id', $new_project->id)->where('user_id', $assignedToId)->exec()->row_count()
            ) {
                return $this->renderNoPermission();
            }

            // Update ticket data
            $data = array(
                'project_id'     => (int) $new_project->id,
                'milestone_id'   => $milestoneId > 0 ? $milestoneId : null,
                'version_id'     => $versionId,
                'component_id'   => $componentId,
                'assigned_to_id' => $assignedToId
            );

            // Set new ticket ID
            $ticket->ticket_id = $new_project->next_tid;
            $new_project->next_tid++;

            // Update ticket
            if ((new TicketManager($ticket, $this->user))->update($data)) {
                $new_project->save();

                // Insert timeline event for old project
                $timeline = new Timeline(array(
                    'project_id' => $this->project->id,
                    'owner_id' => $ticket->id,
                    'action' => 'ticket_moved_to',
                    'data' => $new_project->id,
                    'user_id' => $this->user->id
                ));
                $timeline->save();

                // Insert timeline event for new project
                $timeline = new Timeline(array(
                    'project_id' => $new_project->id,
                    'owner_id' => $ticket->id,
                    'action' => 'ticket_moved_from',
                    'data' => $this->project->id,
                    'user_id' => $this->user->id
                ));
                $timeline->save();

                if ($this->isApi) {
                    return $this->json(['success' => true, 'project' => $new_project->slug, 'ticket_id' => (int) $ticket->ticket_id]);
                }

                return $this->redirectTo($new_project->href("tickets/{$ticket->ticket_id}"));
            }
        }

        View::set(array('ticket' => $ticket, 'next_step' => $next_step));

        return $this->render('tickets/move.phtml');
    }

    /**
     * Returns the project to move a ticket to, if it exists and the user can see it
     * and create tickets in it.
     */
    private function moveDestination(int $projectId): ?Project
    {
        foreach ($this->projects as $project) {
            if ((int) $project->id === $projectId) {
                return $this->user->permission($project->id, 'create_tickets') ? $project : null;
            }
        }

        return null;
    }

    /**
     *
     * Delete ticket.
     */
    #[AuthMiddleware(['delete_tickets'])]
    public function delete(int $ticket_id)
    {
        $ticket = Ticket::findByProjectIdAndTicketId($this->project->id, $ticket_id);

        if (!$ticket) {
            return $this->show404();
        }

        $ticket->delete();
        Request::redirectTo($this->project->href('tickets'));
    }

    /**
     *
     * Mass Actions.
     */
    #[AuthMiddleware(['update_tickets'])]
    public function massActions()
    {
        // Check permission
        if (!$this->user->permission($this->project->id, 'perform_mass_actions')) {
            return $this->renderNoPermission();
        }

        // Decode tickets array
        $tickets = is_array(Request::get('tickets'))
            ? Request::get('tickets')
            : json_decode(Request::get('tickets'), true);

        // Make sure there are some tickets
        if (!is_array($tickets) || !count($tickets)) {
            Request::redirectTo($this->project->href('tickets'));
        }

        // Loop over tickets and process actions
        $input = new TicketInput($this->user, $this->project);
        $comment = $input->comment();
        $properties = $input->bulkChanges();

        if (count($properties) || $comment) {
            foreach ($tickets as $ticket_id) {
                $ticket = Ticket::findByProjectIdAndTicketId($this->project->id, (int) $ticket_id);

                if ($ticket) {
                    (new TicketManager($ticket, $this->user))->update($properties, $comment);
                }
            }
        }

        // Clear selected tickets
        setcookie('selected_tickets', '', time(), '/');

        if (Router::$extension === '.json') {
            return $this->json(['success' => true]);
        } else {
            return $this->redirectTo($this->project->href('tickets'));
        }
    }
}
