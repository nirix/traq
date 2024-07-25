<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace traq\controllers;

use avalon\http\Request;
use avalon\http\Router;
use avalon\output\View;
use avalon\core\Load;
use avalon\http\Response;
use traq\models\Project;
use traq\models\Ticket;
use traq\models\TicketRelationship;
use traq\models\Milestone;
use traq\models\Status;
use traq\models\Type;
use traq\models\Component;
use traq\models\User;
use traq\models\Subscription;
use traq\models\CustomField;
use traq\models\CustomFieldValue;
use traq\models\Timeline;
use traq\helpers\TicketFilterQuery;
use traq\helpers\Pagination;

/**
 * Ticket controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Tickets extends AppController
{
    // Before filters
    public $before = array(
        'view' => array('_check_permission'),
        'new' => array('_check_permission'),
        'edit' => array('_check_permission'),
        'update' => array('_check_permission'),
        'delete' => array('_check_permission')
    );

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
        return $this->renderView('tickets/index.phtml');
    }

    /**
     * Handles the ticket listing index page.
     */
    public function action_api()
    {
        // Atom feed
        $this->feeds[] = [
            Request::requestUri() . ".atom",
            l('x_ticket_feed', $this->project->name)
        ];

        // Create ticket filter query
        $filter_query = new TicketFilterQuery($this->project);

        // Loop over request variables
        foreach (Request::$request as $filter => $value) {
            // Check if the filter exists...
            if (in_array($filter, array_keys(ticket_filters_for($this->project)))) {
                $filter_query->process($filter, $value);
            }
        }

        // Fetch tickets
        $tickets = [];
        $rows = $this->db->select('tickets.*')->from('tickets')->custom_sql($filter_query->sql());

        // Order by creation date for atom feed
        if (Router::$extension == '.atom') {
            $rows->order_by('created_at', 'DESC');
        }
        // Sort from URI, if set
        else {
            // field.direction
            $order = explode('.', ticket_sort_order($this->project->default_ticket_sorting));

            // Check if we need to do
            // anything with the field.
            switch ($order[0]) {
                case 'summary':
                case 'body':
                case 'votes':
                case 'created_at':
                case 'updated_at':
                    $property = $order[0];
                    break;

                case 'user':
                case 'milestone':
                case 'version':
                case 'component':
                case 'type':
                case 'status':
                case 'priority':
                case 'severity':
                case 'assigned_to':
                    $property = "{$order[0]}_id";
                    break;

                case 'id':
                    $property = "ticket_id";
                    break;

                default:
                    $property = 'ticket_id';
            }

            // Order rows
            $rows->order_by($property, (strtolower($order[1]) == 'asc' ? "ASC" : "DESC"));
        }

        // Paginate tickets
        $pagination = new Pagination(
            (isset(Request::$request['page']) ? Request::$request['page'] : 1), // Page
            settings('tickets_per_page'), // Per page
            $rows->exec()->row_count() // Row count
        );

        if ($pagination->paginate) {
            $rows->limit($pagination->limit, settings('tickets_per_page'));
        }

        View::set(compact('pagination'));
        unset($all_rows);

        $customFields = [];
        $ticketCustomFields = [];
        $projectCustomFields = $this->project->custom_fields->exec()->fetch_all();

        foreach ($projectCustomFields as $customField) {
            $customFields[$customField->id] = $customField;
        }

        $customFieldValues = CustomFieldValue::fetch_all();
        foreach ($customFieldValues as $customFieldValue) {
            // $customFields[$customFieldValue->custom_field_id]['values'][$customFieldValue->ticket_id] = $customFieldValue->value;
            if (isset($customFields[$customFieldValue->custom_field_id])) {
                $customField = $customFields[$customFieldValue->custom_field_id];
                $slug = str_replace('-', '_', $customField->slug);
                $ticketCustomFields[$customFieldValue->ticket_id][$slug] = $customFieldValue->value;
            }
        }

        // Add to tickets array
        foreach ($rows->exec()->fetch_all() as $row) {
            $ticket = (new Ticket($row, false))->__toArray();
            $ticket['custom_fields'] = $ticketCustomFields[$ticket['id']] ?? [];

            $tickets[] = $ticket;
        }

        $this->apiResponse([
            'page' => (int) ($pagination->total_pages > 0 ? $pagination->page : 1),
            'total_pages' => (int) $pagination->total_pages,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Handles the view ticket page.
     *
     * @param integer $ticket_id
     */
    public function action_view($ticket_id)
    {
        // Fetch the ticket from the database and send it to the view.
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

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

        // Ticket history
        $ticket_history = $ticket->history;

        switch (settings('ticket_history_sorting')) {
            case 'oldest_first':
                $ticket_history->order_by('created_at', 'ASC');
                break;

            case 'newest_first':
                $ticket_history->order_by('created_at', 'DESC');
                break;
        }

        $ticket_history = $ticket_history->exec()->fetch_all();

        // Does ticket exist?
        if (!$ticket) {
            return $this->show404();
        }

        // Atom feed
        $this->feeds[] = array(Request::requestUri() . ".atom", l('x_x_history_feed', $this->project->name, $ticket->summary));

        // Set title and send ticket to view
        $this->title($ticket->summary);
        View::set('ticket', $ticket);
        View::set('attachments', $ticket->attachments->exec()->fetchAll());
        View::set('ticket_history', $ticket_history);
    }

    /**
     * Handles the add vote page.
     *
     * @param integer $ticket_id
     */
    public function action_vote($ticket_id)
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
    }

    /**
     * Handles the voters page.
     *
     * @param integer $ticket_id
     */
    public function action_voters($ticket_id)
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
    }

    /**
     * Handles the new ticket page and ticket creation.
     */
    public function action_new()
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
        if (Request::method() == 'post') {
            // Set the ticket data
            $data = array(
                'summary'      => Request::post('summary'),
                'body'         => Request::post('description'),
                'user_id'      => $this->user->id,
                'project_id'   => $this->project->id,
                'milestone_id' => 0,
                'version_id'   => 0,
                'component_id' => 0,
                'type_id'      => Request::post('type', 1),
                'severity_id'  => 4,
                'tasks'        => array(),
                'is_private'   => Request::post('is_private') ? 1 : 0
            );

            // Milestone
            if ($this->user->permission($this->project->id, 'ticket_properties_set_milestone')) {
                $data['milestone_id'] = Request::post('milestone');
            }

            // Version
            if ($this->user->permission($this->project->id, 'ticket_properties_set_version')) {
                $data['version_id'] = Request::post('version');
            }

            // Component
            if ($this->user->permission($this->project->id, 'ticket_properties_set_component')) {
                $data['component_id'] = Request::post('component');
            }

            // Severity
            if ($this->user->permission($this->project->id, 'ticket_properties_set_severity')) {
                $data['severity_id'] = Request::post('severity', 4);
            }

            // Priority
            if ($this->user->permission($this->project->id, 'ticket_properties_set_priority')) {
                $data['priority_id'] = Request::post('priority', 3);
            }

            // Status
            if ($this->user->permission($this->project->id, 'ticket_properties_set_status')) {
                $data['status_id'] = Request::post('status');
            }

            // Assigned to
            if ($this->user->permission($this->project->id, 'ticket_properties_set_assigned_to')) {
                $data['assigned_to_id'] = Request::post('assigned_to');
            }

            // Ticket tasks
            if ($this->user->permission($this->project->id, 'ticket_properties_set_tasks') and Request::post('tasks') != null) {
                $tasks = json_decode(Request::post('tasks'), true);

                foreach ($tasks as $id => $task) {
                    if (is_array($task) and !empty($task['task'])) {
                        $data['tasks'][] = $task;
                    }
                }
            }

            // Time proposed
            if ($this->user->permission($this->project->id, 'ticket_properties_set_time_worked')) {
                $data['time_proposed'] = Request::post('time_proposed');
            }

            // Time worked
            if ($this->user->permission($this->project->id, 'ticket_properties_set_time_proposed')) {
                $data['time_worked'] = Request::post('time_worked');
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
                    foreach (explode(',', Request::post('related_tickets')) as $ticket_id) {
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

                if ($this->is_api) {
                    return \traq\helpers\API::response(1, array('ticket' => $ticket));
                } else {
                    Request::redirectTo($ticket->href());
                }
            }
        }

        View::set('ticket', $ticket);
    }

    /**
     * Handles the updating of the ticket.
     */
    public function action_update($ticket_id)
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
            $data['summary'] = Request::post('summary', $ticket->summary);
        }

        // Type
        if ($this->user->permission($this->project->id, 'ticket_properties_change_type')) {
            $data['type_id'] = Request::post('type', $ticket->type->id);
        }

        // Milestone
        if ($this->user->permission($this->project->id, 'ticket_properties_change_milestone')) {
            $data['milestone_id'] = Request::post('milestone', $ticket->milestone_id);
        }

        // Version
        if ($this->user->permission($this->project->id, 'ticket_properties_change_version')) {
            $data['version_id'] = Request::post('version', $ticket->version_id);
        }

        // Component
        if ($this->user->permission($this->project->id, 'ticket_properties_change_component')) {
            $data['component_id'] = Request::post('component', $ticket->component_id);
        }

        // Severity
        if ($this->user->permission($this->project->id, 'ticket_properties_change_severity')) {
            $data['severity_id'] = Request::post('severity', $ticket->severity_id);
        }

        // Priority
        if ($this->user->permission($this->project->id, 'ticket_properties_change_priority')) {
            $data['priority_id'] = Request::post('priority', $ticket->priority_id);
        }

        // Status
        if ($this->user->permission($this->project->id, 'ticket_properties_change_status')) {
            $data['status_id'] = Request::post('status', $ticket->status_id);
        }

        // Assigned to
        if ($this->user->permission($this->project->id, 'ticket_properties_change_assigned_to')) {
            $data['assigned_to_id'] = Request::post('assigned_to', $ticket->assigned_to_id);
        }

        // Ticket tasks
        if ($this->user->permission($this->project->id, 'ticket_properties_change_tasks') and Request::post('tasks') != null) {
            $data['tasks'] = array();
            $tasks = json_decode(Request::post('tasks'), true);

            foreach ($tasks as $id => $task) {
                if (is_array($task) and !empty($task['task'])) {
                    $data['tasks'][] = $task;
                }
            }
        }

        // Time proposed
        if ($this->user->permission($this->project->id, 'ticket_properties_change_time_worked')) {
            $data['time_proposed'] = Request::post('time_proposed');
        }

        // Time worked
        if ($this->user->permission($this->project->id, 'ticket_properties_change_time_proposed')) {
            $data['time_worked'] = Request::post('time_worked');
        }

        // Related tickets
        if ($this->user->permission($this->project->id, 'ticket_properties_change_related_tickets')) {
            $related_tickets = $ticket->related_ticket_tids();
            $posted_related_tickets = array();

            foreach (explode(',', Request::post('related_tickets')) as $posted_related_ticket) {
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
        if ($this->user->permission($this->project->id, 'add_attachments') and isset($_FILES['attachment']) and isset($_FILES['attachment']['name'])) {
            $data['attachment'] = $_FILES['attachment']['name'];
        }

        $data['is_private'] = Request::post('is_private', 0);

        // Custom fields, FUN!
        if (isset(Request::$post['custom_fields'])) {
            $this->process_custom_fields($ticket, Request::$post['custom_fields']);
        }

        // Update the ticket
        if ($ticket->update_data($data)) {
            if ($this->is_api) {
                return \API::response(1, array('ticket' => $ticket));
            } else {
                return Request::redirectTo($ticket->href());
            }
        }

        $this->action_view($ticket->id);
        View::set(compact('ticket'));
        $this->render['view'] = 'tickets/view';
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
     * Handles the editing of the ticket description.
     */
    public function action_edit($ticket_id)
    {
        // Get the ticket
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

        // Set the title
        $this->title($ticket->summary);
        $this->title(l('edit_ticket'));

        // Has the form been submitted?
        if (Request::method() == 'post') {
            // Set the ticket body
            $ticket->body = Request::$post['body'];

            // Save and redirect
            if ($ticket->save()) {
                Request::redirect(Request::base($ticket->href()));
            }
        }

        View::set('ticket', $ticket);
    }

    /**
     * Move ticket.
     *
     * @param integer $ticket_id
     */
    public function action_move($ticket_id)
    {
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();
        $next_step = 2;

        // Step 2
        if (Request::post('step') == 2) {
            $next_step = 3;
            $new_project = Project::find(Request::$post['project_id']);
            View::set('new_project', $new_project);
        }
        // Step 3
        elseif (Request::post('step') == 3 or $this->is_api) {
            $next_step = 2;
            $new_project = Project::find(Request::post('project_id'));

            // Update ticket data
            $data = array(
                'project_id'     => Request::post('project_id'),
                'milestone_id'   => Request::post('milestone_id'),
                'version_id'     => Request::post('version_id', 0),
                'component_id'   => Request::post('component_id', 0),
                'assigned_to_id' => Request::post('assigned_to_id', 0)
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
    }

    /**
     * Delete ticket.
     */
    public function action_delete($ticket_id)
    {
        // Get ticket, delete it then redirect to ticket listing
        $ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();
        $ticket->delete();
        Request::redirectTo($this->project->href('tickets'));
    }

    /**
     * Mass Actions.
     */
    public function action_mass_actions()
    {
        // Check permission
        if (!$this->user->permission($this->project->id, 'perform_mass_actions')) {
            return $this->show_no_permission();
        }

        // Decode tickets array
        $tickets = is_array(Request::post('tickets'))
            ? Request::post('tickets')
            : $json_decode(Request::post('tickets'), true);

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
                && Request::post('type', -1) != -1
            ) {
                $data['type_id'] = Request::post('type');
            }

            // Milestone
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_milestone')
                && Request::post('milestone', -1) != -1
            ) {
                $data['milestone_id'] = Request::post('milestone');
            }

            // Version
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_version')
                && Request::post('version', -1) != -1
            ) {
                $data['version_id'] = Request::post('version');
            }

            // Component
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_component')
                && Request::post('component', -1) != -1
            ) {
                $data['component_id'] = Request::post('component');
            }

            // Severity
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_severity')
                && Request::post('severity', -1) != -1
            ) {
                $data['severity_id'] = Request::post('severity');
            }

            // Priority
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_priority')
                && Request::post('priority', -1) != -1
            ) {
                $data['priority_id'] = Request::post('priority');
            }

            // Status
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_status')
                && Request::post('status', -1) != -1
            ) {
                $data['status_id'] = Request::post('status');
            }

            // Assigned to
            if (
                $this->user->permission($this->project->id, 'ticket_properties_change_assigned_to')
                && Request::post('assigned_to', -1) != -1
            ) {
                $data['assigned_to_id'] = Request::post('assigned_to');
            }

            if (count($data) or Request::post('comment')) {
                $ticket->update_data($data);
                $ticket->save();
            }
        }

        // Clear selected tickets
        setcookie('selected_tickets', '', time(), '/');

        if (Router::$extension === '.json') {
            $this->apiResponse(['success' => true]);
        } else {
            Request::redirectTo($this->project->href('tickets'));
        }
    }

    /**
     * Used to check the permission for the requested action.
     */
    public function _check_permission($method)
    {
        // Set the proper action depending on the method
        switch ($method) {
                // View ticket
            case 'view':
                $action = 'view_tickets';
                break;

                // Create ticket
            case 'new':
                $action = 'create_tickets';
                break;

                // Edit ticket description
            case 'edit':
                $action = 'edit_ticket_description';
                break;

                // Update ticket properties
            case 'update':
                $action = 'update_tickets';
                break;

                // Delete tickets
            case 'delete':
                $action = 'delete_tickets';
                break;
        }

        // Check if the user has permission
        if (!current_user()->permission($this->project->id, $action)) {
            // oh noes! display the no permission page.
            $this->show_no_permission();
            return false;
        }
    }
}
