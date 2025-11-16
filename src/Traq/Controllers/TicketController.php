<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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
use Traq\Models\Ticket;
use Traq\Models\TicketRelationship;
use Traq\Models\User;
use Traq\Models\Subscription;
use Traq\Models\CustomField;
use Traq\Models\Timeline;
use traq\helpers\Pagination;
use Traq\Middleware\AuthMiddleware;
use Traq\Models\Attachment;
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
        if (Request::method() === 'POST') {
            return $this->create();
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
        [$sortField, $sortDirection] = explode('.', $this->project->default_ticket_sorting);

        $allowedColumns = [
            'ticket_id' => 't.ticket_id',
            'summary' => 't.summary',
            'votes' => 't.votes',
            'created_at' => 't.created_at',
            'updated_at' => 't.updated_at',
            'user' => 'user',
            'assigned_to' => 'assigned_to',
            'milestone' => 'milestone',
            'component' => 'component',
            'type' => 'type',
            'status' => 'status',
            'priority' => 'p.id',
            'severity' => 'sv.id',
        ];

        foreach ($this->project->getCustomFields() as $index => $field) {
            $allowedColumns[$field->slug] = "cf_{$index}.value";
        }

        if (Request::get('order_by')) {
            $sortBits = explode('.', Request::get('order_by'));
            $sortField = isset($allowedColumns[$sortBits[0]]) ? $allowedColumns[$sortBits[0]] : 't.ticket_id';
            $sortDirection = strtoupper($sortBits[1]) === 'ASC' ? 'ASC' : 'DESC';
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

        // If the ticket is private, only allow admins, projects members and the creator to view the ticket.
        if ($ticket->is_private) {
            if (
                $this->user->id !== $ticket->user_id
                && !$this->user->group->is_admin
                && !$this->project->is_member($this->user)
            ) {
                return $this->show_no_permission();
            }
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

    /**
     * Handles the add vote page.
     *
     * @param integer $ticket_id
     */
    public function vote(int $ticket_id)
    {
        // Get the ticket
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

        // Don't let the owner vote on their own ticket
        if ($this->user->id == $ticket->user_id) {
            return false;
        }

        // Does the user have permission to vote on tickets?
        if (!$this->user->permission($this->project->id, 'vote_on_tickets')) {
            View::set('error', l('errors.must_be_logged_in'));
        }
        // Cast the vote
        elseif ($ticket->add_vote($this->user->id)) {
            $ticket->save();
            View::set('ticket', $ticket);
            View::set('error', false);
        }
        // They've already voted...
        else {
            View::set('error', l('errors.already_voted'));
        }

        $this->render['layout'] = false;
        return $this->render('tickets/vote.js.php');
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

        $voters = array();

        // Have there been any votes?
        if (isset($ticket->extra['voted']) and is_array($ticket->extra['voted'])) {
            // Populate the voters array
            foreach ($ticket->extra['voted'] as $voter) {
                $voters[] = User::find($voter);
            }
        }

        View::set('voters', $voters);

        $this->render['layout'] = false;
        if (Request::get('overlay') === 'true') {
            return $this->render('tickets/voters.overlay.phtml');
        }

        return $this->render('tickets/voters.popover.phtml');
    }

    /**
     *
     * Handles the new ticket page and ticket creation.
     */
    #[AuthMiddleware(['create_tickets'])]
    public function create()
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

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Set the ticket data
            $data = array(
                'summary'      => Request::get('summary'),
                'body'         => Request::get('description'),
                'user_id'      => $this->user->id,
                'project_id'   => $this->project->id,
                'milestone_id' => null,
                'version_id'   => null,
                'component_id' => null,
                'type_id'      => Request::get('type', 1),
                'severity_id'  => 4,
                'tasks'        => [],
                'is_private'   => Request::get('is_private') ? 1 : 0
            );

            // Milestone
            if ($this->user->permission($this->project->id, 'ticket_properties_set_milestone')) {
                $data['milestone_id'] = (int) Request::get('milestone') === 0 ? null : (int) Request::get('milestone');
            }

            // Version
            if ($this->user->permission($this->project->id, 'ticket_properties_set_version')) {
                $data['version_id'] = (int) Request::get('version') === 0 ? null : (int) Request::get('version');
            }

            // Component
            if ($this->user->permission($this->project->id, 'ticket_properties_set_component')) {
                $data['component_id'] = (int) Request::get('component') === 0 ? null : (int) Request::get('component');
            }

            // Severity
            if ($this->user->permission($this->project->id, 'ticket_properties_set_severity')) {
                $data['severity_id'] = (int) Request::get('severity', 4);
            }

            // Priority
            if ($this->user->permission($this->project->id, 'ticket_properties_set_priority')) {
                $data['priority_id'] = (int) Request::get('priority', 3);
            }

            // Status
            if ($this->user->permission($this->project->id, 'ticket_properties_set_status')) {
                $data['status_id'] = (int) Request::get('status', 1);
            }

            // Assigned to
            if ($this->user->permission($this->project->id, 'ticket_properties_set_assigned_to')) {
                $data['assigned_to_id'] = (int) Request::get('assigned_to') === 0 ? null : (int) Request::get('assigned_to');
            }

            // Ticket tasks
            if ($this->user->permission($this->project->id, 'ticket_properties_set_tasks') && Request::get('tasks') != null) {
                $tasks = json_decode(Request::get('tasks'), true);

                foreach ($tasks as $id => $task) {
                    if (is_array($task) && !empty($task['task'])) {
                        $data['tasks'][] = $task;
                    }
                }
            }

            // Time proposed
            if ($this->user->permission($this->project->id, 'ticket_properties_set_time_worked')) {
                $data['time_proposed'] = Request::get('time_proposed');
            }

            // Time worked
            if ($this->user->permission($this->project->id, 'ticket_properties_set_time_proposed')) {
                $data['time_worked'] = Request::get('time_worked');
            }

            // Set the ticket data
            $ticket->set($data);

            // Custom fields, FUN!
            if (isset(Request::$post['custom_fields'])) {
                $this->process_custom_fields($ticket, Request::$post['custom_fields']);
            }

            // Check if the ticket data is valid...
            // if it is, save the ticket to the DB and
            // redirect to the ticket page.
            if (($this->user->group->is_admin || check_ticket_creation_delay($ticket)) && $ticket->is_valid()) {
                // Set last ticket creation time
                $_SESSION['last_ticket_creation'] = time();

                $ticket->save();

                // Related tickets
                if ($this->user->permission($this->project->id, 'ticket_properties_set_related_tickets')) {
                    foreach (explode(',', Request::get('related_tickets', '')) as $ticket_id) {
                        $related = Ticket::select('id')
                            ->where('project_id', $this->project->id)
                            ->where('ticket_id', trim($ticket_id))
                            ->limit(1)->exec()->fetch();

                        if ($related) {
                            $relation = new TicketRelationship(array(
                                'ticket_id'         => $ticket->id,
                                'related_ticket_id' => $related->id
                            ));

                            $relation->save();
                        }
                    }
                }

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
     *
     * Handles the updating of the ticket.
     */
    #[AuthMiddleware(['update_tickets'])]
    public function update(int $ticket_id)
    {
        // Get the ticket
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

        // Set the title
        $this->title($ticket->summary);
        $this->title(l('update_ticket'));

        // Collect the new data
        $data = array(
            'summary'      => $ticket->summary,
            'milestone_id' => $ticket->milestone_id,
            'version_id'   => $ticket->version_id,
            'component_id' => $ticket->component_id,
            'type_id'      => $ticket->type_id,
            'severity_id'  => $ticket->severity_id,
            'priority_id'  => $ticket->priority_id,
            'status_id'    => $ticket->status_id,
            'tasks'        => $ticket->tasks,
            'is_private'   => $ticket->is_private
        );

        // Summary
        if ($this->user->permission($this->project->id, 'ticket_properties_change_summary')) {
            $data['summary'] = Request::get('summary', $ticket->summary);
        }

        // Type
        if ($this->user->permission($this->project->id, 'ticket_properties_change_type')) {
            $data['type_id'] = Request::get('type', $ticket->type->id);
        }

        // Milestone
        if ($this->user->permission($this->project->id, 'ticket_properties_change_milestone')) {
            $data['milestone_id'] = Request::get('milestone', $ticket->milestone_id);
        }

        // Version
        if ($this->user->permission($this->project->id, 'ticket_properties_change_version')) {
            $data['version_id'] = Request::get('version', $ticket->version_id);
        }

        // Component
        if ($this->user->permission($this->project->id, 'ticket_properties_change_component')) {
            $data['component_id'] = Request::get('component', $ticket->component_id);
        }

        // Severity
        if ($this->user->permission($this->project->id, 'ticket_properties_change_severity')) {
            $data['severity_id'] = Request::get('severity', $ticket->severity_id);
        }

        // Priority
        if ($this->user->permission($this->project->id, 'ticket_properties_change_priority')) {
            $data['priority_id'] = Request::get('priority', $ticket->priority_id);
        }

        // Status
        if ($this->user->permission($this->project->id, 'ticket_properties_change_status')) {
            $data['status_id'] = Request::get('status', $ticket->status_id);
        }

        // Assigned to
        if ($this->user->permission($this->project->id, 'ticket_properties_change_assigned_to')) {
            $data['assigned_to_id'] = Request::get('assigned_to', $ticket->assigned_to_id);
        }

        // Ticket tasks
        if ($this->user->permission($this->project->id, 'ticket_properties_change_tasks') && Request::get('tasks') != null) {
            $data['tasks'] = array();
            $tasks = json_decode(Request::get('tasks'), true);

            foreach ($tasks as $id => $task) {
                if (is_array($task) and !empty($task['task'])) {
                    $data['tasks'][] = $task;
                }
            }
        }

        // Time proposed
        if ($this->user->permission($this->project->id, 'ticket_properties_change_time_worked')) {
            $data['time_proposed'] = Request::get('time_proposed');
        }

        // Time worked
        if ($this->user->permission($this->project->id, 'ticket_properties_change_time_proposed')) {
            $data['time_worked'] = Request::get('time_worked');
        }

        // Related tickets
        if ($this->user->permission($this->project->id, 'ticket_properties_change_related_tickets')) {
            $related_tickets = $ticket->relatedTicketTids();
            $posted_related_tickets = array();

            foreach (explode(',', Request::get('related_tickets')) as $posted_related_ticket) {
                $posted_related_tickets[] = trim($posted_related_ticket);
            }

            // New relations
            foreach ($posted_related_tickets as $related_tid) {
                // Make sure it's not already a relation
                if (!in_array($related_tid, $related_tickets)) {
                    // Fetch ticket info
                    $related_ticket = Ticket::select('id')
                        ->where('project_id', $this->project->id)
                        ->where('ticket_id', $related_tid)
                        ->exec()->fetch();

                    // Make sure the ticket exists
                    if ($related_ticket) {
                        $relation = new TicketRelationship(array(
                            'ticket_id' => $ticket->id,
                            'related_ticket_id' => $related_ticket->id
                        ));
                        $relation->save();
                    }
                }
            }

            // Delete relations
            foreach ($ticket->ticket_relationships->exec()->fetch_all() as $relation) {
                if (!in_array($relation->related_ticket->ticket_id, $posted_related_tickets)) {
                    $relation->delete();
                }
            }
        }

        // Check if we're adding an attachment and that the user has permission to do so
        if ($this->user->permission($this->project->id, 'add_attachments') && isset($_FILES['attachment']) && isset($_FILES['attachment']['name'])) {
            $data['attachment'] = $_FILES['attachment']['name'];
        }

        $data['is_private'] = Request::get('is_private', 0);

        // Custom fields, FUN!
        if (isset(Request::$post['custom_fields'])) {
            $this->process_custom_fields($ticket, Request::$post['custom_fields']);
        }

        // Update the ticket
        if ($ticket->update_data($data)) {
            if ($this->isJson) {
                return $this->json(['ticket' => $ticket]);
            } else {
                return $this->redirectTo($ticket->href());
            }
        }

        $this->set($this->getTicketData($ticket));

        return $this->render('tickets/view.phtml', ['ticket' => $ticket]);
    }

    /**
     * Processes the custom fields
     *
     * @param object $ticket
     * @param array  $custom_fields
     */
    private function process_custom_fields(&$ticket, $fields)
    {
        foreach ($this->custom_fields as $field) {
            if (in_array($ticket->type_id, $field->ticket_type_ids) or $field->ticket_type_ids[0] == 0) {
                if (isset($fields[$field->id])) {
                    if ($field->validate($fields[$field->id])) {
                        $ticket->set_custom_field($field->id, $field->name, $fields[$field->id]);
                    } else {
                        $ticket->_add_error($field->id, l("errors.custom_fields.x_is_not_valid", $field->name, $field->type));
                    }
                }

                // Check if field is required
                if ($field->is_required and empty($fields[$field->id])) {
                    $ticket->_add_error($field->id, l('errors.custom_fields.x_required', $field->name));
                }
            }
        }
    }

    /**
     *
     * Handles the editing of the ticket description.
     */
    #[AuthMiddleware(['edit_ticket_description'])]
    public function edit(int $ticket_id)
    {
        // Get the ticket
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

        // Set the title
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
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();
        $next_step = 2;

        // Step 2
        if (Request::get('step') == 2) {
            $next_step = 3;
            $new_project = Project::find(Request::$post['project_id']);
            View::set('new_project', $new_project);
        }
        // Step 3
        elseif (Request::get('step') == 3 || $this->isApi) {
            $next_step = 2;
            $new_project = Project::find(Request::get('project_id'));

            // Update ticket data
            $data = array(
                'project_id'     => Request::get('project_id'),
                'milestone_id'   => Request::get('milestone_id'),
                'version_id'     => Request::get('version_id', 0),
                'component_id'   => Request::get('component_id', 0),
                'assigned_to_id' => Request::get('assigned_to_id', 0)
            );

            // Set new ticket ID
            $ticket->ticket_id = $new_project->next_tid;
            $new_project->next_tid++;

            // Update ticket
            if ($ticket->update_data($data)) {
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

                Request::redirectTo($new_project->href("tickets/{$ticket->ticket_id}"));
            }
        }

        View::set(array('ticket' => $ticket, 'next_step' => $next_step));

        return $this->render('tickets/move.phtml');
    }

    /**
     *
     * Delete ticket.
     */
    #[AuthMiddleware(['delete_tickets'])]
    public function delete(int $ticket_id)
    {
        // Get ticket, delete it then redirect to ticket listing
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();
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
            return $this->show_no_permission();
        }

        // Decode tickets array
        $tickets = is_array(Request::get('tickets'))
            ? Request::get('tickets')
            : json_decode(Request::get('tickets'), true);

        // Make sure there are some tickets
        if (!is_array($tickets) and !count($tickets)) {
            Request::redirectTo($this->project->href('tickets'));
        }

        // Loop over tickets and process actions
        foreach ($tickets as $ticket_id) {
            $ticket = Ticket::select('*')->where('project_id', $this->project->id)->where('ticket_id', $ticket_id)->exec()->fetch();
            $data = [];

            // Type
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_type')
                && Request::get('type', -1) != -1
            ) {
                $data['type_id'] = Request::get('type');
            }

            // Milestone
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_milestone')
                && Request::get('milestone', -1) != -1
            ) {
                $data['milestone_id'] = Request::get('milestone');
            }

            // Version
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_version')
                && Request::get('version', -1) != -1
            ) {
                $data['version_id'] = Request::get('version');
            }

            // Component
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_component')
                && Request::get('component', -1) != -1
            ) {
                $data['component_id'] = Request::get('component');
            }

            // Severity
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_severity')
                && Request::get('severity', -1) != -1
            ) {
                $data['severity_id'] = Request::get('severity');
            }

            // Priority
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_priority')
                && Request::get('priority', -1) != -1
            ) {
                $data['priority_id'] = Request::get('priority');
            }

            // Status
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_status')
                && Request::get('status', -1) != -1
            ) {
                $data['status_id'] = Request::get('status');
            }

            // Assigned to
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_assigned_to')
                && Request::get('assigned_to', -1) != -1
            ) {
                $data['assigned_to_id'] = Request::get('assigned_to');
            }

            if (count($data) or Request::get('comment')) {
                $ticket->update_data($data);
                $ticket->save();
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
