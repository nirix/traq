<?php
/*
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
	/**
	 * Handles the ticket listing index page.
	 */
	public function action_index()
	{
		$this->title(l('tickets'));
		Load::helper('tickets');
		
		$sql = array();
		$sql[] = "`project_id` = '{$this->project->id}'";
		
		// Filters
		foreach (Request::$request as $filter => $value)
		{
			// Check if the filter exists...
			if (!in_array($filter, ticket_filters()))
			{
				continue;
			}
			
			$filter_sql = array();
			
			// Milestone filter
			if ($filter == 'milestone')
			{
				foreach (explode(',', $value) as $name)
				{
					$milestone = Milestone::find('slug', $name);
					$filter_sql[] = $milestone->id;
				}
				$sql[] = "milestone_id IN (" . implode(', ', $filter_sql) . ")";
			}
			// Status filter
			elseif ($filter == 'status')
			{
				if ($value == 'open' or $value == 'closed')
				{
					foreach (TicketStatus::select('id')->where('status', ($value == 'open' ? 1 : 0))->exec()->fetch_all() as $status)
					{
						$filter_sql[] = $status->id;
					}
				}
				else
				{
					foreach (explode(',', $value) as $name)
					{
						$status = TicketStatus::find('name', urldecode($name));
						$filter_sql[] = $status->id;
					}
				}
				$sql[] = "status_id IN (" . implode(', ', $filter_sql) . ")";
			}
			// Type filter
			elseif ($filter == 'type')
			{
				foreach (explode(',', $value) as $name)
				{
					$type = TicketType::find('name', urldecode($name));
					$filter_sql[] = $type->id;
				}
				$sql[] = "type_id IN (" . implode(', ', $filter_sql) . ")";
			}
			// Component filter
			elseif ($filter == 'component')
			{
				foreach (explode(',', $value) as $name)
				{
					$component = Component::find('name', urldecode($name));
					$filter_sql[] = $component->id;
				}
				$sql[] = "component_id IN (" . implode(', ', $filter_sql) . ")";
			}
		}
		
		// Fetch tickets
		$tickets = array();
		$rows = $this->db->select()->from('tickets')->custom_sql(count($sql) > 0 ? 'WHERE ' . implode(' AND ', $sql) :'')->exec()->fetch_all();
		foreach($rows as $row)
		{
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
		$this->title(l('tickets'));
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
		$ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

		if (!$this->user->permission($this->project->id, 'vote_on_tickets'))
		{
			View::set('error', l('errors.must_be_logged_in'));
		}
		elseif ($ticket->add_vote($this->user->id))
		{
			$ticket->save();
			View::set('ticket', $ticket);
			View::set('error', false);
		}
		else
		{
			View::set('error', l('errors.already_voted'));
		}
	}
	
	/**
	 * Handles the new ticket page and ticket creation.
	 */
	public function action_new()
	{
		$ticket = new Ticket;
		View::set('ticket', $ticket);
		
		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
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
			$ticket->set($data);
			
			// Check if the ticket data is valid...
			// if it is, save the ticket to the DB and
			// redirect to the ticket page.
			if ($ticket->save())
			{
				Request::redirect(Request::base($ticket->href()));
			}
		}
	}

	/**
	 * Handles the editing of the ticket description.
	 */
	public function action_edit($ticket_id)
	{
		if (!$this->user->permission($this->project->id, 'edit_ticket_description'))
		{
			$this->show_no_permission();
		}

		$ticket = Ticket::select()->where("ticket_id", $ticket_id)->where("project_id", $this->project->id)->exec()->fetch();

		if (Request::$method == 'post')
		{
			$ticket->body = Request::$post['body'];

			if ($ticket->save())
			{
				Request::redirect(Request::base($ticket->href()));
			}
		}

		View::set('ticket', $ticket);
	}
}