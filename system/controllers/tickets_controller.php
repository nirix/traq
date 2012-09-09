<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

use avalon\core\Load;

/**
 * Ticket controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class TicketsController extends AppController
{
	// Before filters
	public $_before = array(
		'new' => array('_check_permission'),
		'edit' => array('_check_permission'),
		'update' => array('_check_permission'),
		'delete' => array('_check_permission')
	);

	/**
	 * Custom constructor, we need to do extra stuff.
	 */
	public function __construct()
	{
		parent::__construct();

		// Set the title and load the helper
		$this->title(l('tickets'));
		Load::helper('tickets');
	}

	/**
	 * Handles the ticket listing index page.
	 */
	public function action_index()
	{
		// Start the filter SQL query
		$sql = array();
		$sql[] = "`project_id` = '{$this->project->id}'";

		// Filters
		$filters = array();
		foreach (Request::$request as $filter => $value) {
			// Check if the filter exists...
			if (!in_array($filter, ticket_filters())) {
				continue;
			}

			$filter_sql = array();
			$prefix = '';
			if ($value[0] == '!') {
				$prefix = '!';
				$value = substr($value, 1);
			}

			// Milestone and Version filter
			if ($filter == 'milestone' or $filter == 'version') {
				foreach (explode(',', $value) as $name) {
					$milestone = Milestone::find('slug', $name);
					$filter_sql[] = $milestone->id;
				}
				$sql[] = "{$filter}_id " . ($prefix == '!' ? 'NOT' :'') . " IN (" . implode(', ', $filter_sql) . ")";
			}
			// Status filter
			elseif ($filter == 'status') {
				// All open, or all closed statuses
				if ($value == 'allopen' or $value == 'allclosed') {
					foreach (Status::select('id')->where('status', ($value == 'allopen' ? 1 : 0))->exec()->fetch_all() as $status) {
						$filter_sql[] = $status->id;
					}
				}
				// Get each status by name
				else {
					foreach (explode(',', $value) as $name) {
						$status = Status::find('name', urldecode($name));
						$filter_sql[] = $status->id;
					}
				}
				$sql[] = "status_id " . ($prefix == '!' ? 'NOT' :'') . " IN (" . implode(', ', $filter_sql) . ")";
			}
			// Type filter
			elseif ($filter == 'type') {
				foreach (explode(',', $value) as $name) {
					$type = Type::find('name', urldecode($name));
					$filter_sql[] = $type->id;
				}
				$sql[] = "type_id IN " . ($prefix == '!' ? 'NOT' :'') . " (" . implode(', ', $filter_sql) . ")";
			}
			// Component filter
			elseif ($filter == 'component') {
				foreach (explode(',', $value) as $name){
					$component = Component::find('name', urldecode($name));
					$filter_sql[] = $component->id;
				}
				$sql[] = "component_id " . ($prefix == '!' ? 'NOT' :'') . " IN (" . implode(', ', $filter_sql) . ")";
			}

			$filters[$filter] = array('prefix' => $prefix, 'values' => $filter_sql);
		}

		View::set('filters', $filters);

		// Fetch tickets
		$tickets = array();
		$rows = $this->db->select()->from('tickets')->custom_sql(count($sql) > 0 ? 'WHERE ' . implode(' AND ', $sql) :'')->order_by('priority_id', 'ASC')->exec()->fetch_all();
		foreach($rows as $row) {
			$tickets[] = new Ticket($row, false);
		}

		// Send the tickets array to the view..
		View::set('tickets', $tickets);
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
		$this->title($ticket->summary);
		View::set('ticket', $ticket);
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
		$ticket = new Ticket;

		// Check if the form has been submitted
		if (Request::$method == 'post') {
			// Set the ticket data
			$data = array(
				'summary' => Request::$post['summary'],
				'body' => Request::$post['description'],
				'user_id' => $this->user->id,
				'project_id' => $this->project->id,
				'milestone_id' => Request::$post['milestone'],
				'version_id' => Request::$post['version'],
				'component_id' => Request::$post['component'],
				'type_id' => Request::$post['type'],
				'severity_id' => Request::$post['severity'],
			);

			// Does the user have permission to set all properties?
			if ($this->user->permission($this->project->id, 'set_all_ticket_properties')) {
				$data['priority_id'] = Request::$post['priority'];
				$data['status_id'] = Request::$post['status'];
				$data['assigned_to_id'] = Request::$post['assigned_to'];
			}

			// Set the ticket data
			$ticket->set($data);

			// Check if the ticket data is valid...
			// if it is, save the ticket to the DB and
			// redirect to the ticket page.
			if ($ticket->save()) {
				Request::redirect(Request::base($ticket->href()));
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
			'summary' => Request::$post['summary'],
			'milestone_id' => Request::$post['milestone'],
			'version_id' => Request::$post['version'],
			'component_id' => Request::$post['component'],
			'type_id' => Request::$post['type'],
			'severity_id' => Request::$post['severity']
		);

		// Check the users permission to set the restricted data
		if ($this->user->permission($this->project->id, 'set_all_ticket_properties')) {
			$data['priority_id'] = Request::$post['priority'];
			$data['status_id'] = Request::$post['status'];
			$data['assigned_to_id'] = Request::$post['assigned_to'];
		}

		// Check if we're adding an attachment and that the user has permission to do so
		if ($this->user->permission($this->project->id, 'add_attachments') and isset($_FILES['attachment']) and isset($_FILES['attachment']['name'])) {
			$data['attachment'] = $_FILES['attachment']['name'];
		}

		// Update the ticket
		if ($ticket->update_data($data)) {
			Request::redirect(Request::base($ticket->href()));
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
		if (Request::$method == 'post') {
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
	 * Used to check the permission for the requested action.
	 */
	public function _check_permission($method)
	{
		// Set the proper action depending on the method
		switch($method) {
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