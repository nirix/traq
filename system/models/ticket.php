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

/**
 * Ticket model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Ticket extends Model
{
	protected static $_name = 'tickets';
	protected static $_properties = array(
		'id',
		'ticket_id',
		'summary',
		'body',
		'user_id',
		'project_id',
		'milestone_id',
		'version_id',
		'component_id',
		'type_id',
		'status_id',
		'priority_id',
		'severity_id',
		'assigned_to_id',
		'is_closed',
		'is_private',
		'votes',
		'extra',
		'created_at',
		'updated_at'
	);

	protected $_original_data = array();
	
	protected static $_has_many = array(
		'history' => array('model' => 'tickethistory')
	);
	
	protected static $_belongs_to = array(
		'user', 'project', 'milestone', 'component',
		'priority', 'severity',

		// Relations with different models and such
		'assigned_to' => array('model' => 'user'),
		'version'     => array('model' => 'milestone'),
		'status'      => array('model' => 'ticketstatus'),
		'type'        => array('model' => 'tickettype')
	);

	protected static $_filters_after = array(
		'construct' => array('process_data_read')
	);

	protected static $_filters_before = array(
		'create' => array('process_data_write'),
		'save' => array('process_data_write')
	);
	
	/**
	 * Returns the URI for the ticket.
	 *
	 * @return string
	 */
	public function href()
	{
		return "/{$this->project->slug}/tickets/{$this->ticket_id}";
	}

	/**
	 * Adds a vote to the ticket.
	 *
	 * @param object $user
	 *
	 * @return bool
	 */
	public function add_vote($user_id)
	{
		if (!is_array($this->_data['extra']['voted']))
		{
			$this->_data['extra']['voted'] = array();
		}

		if (!in_array($user_id, $this->_data['extra']['voted']))
		{
			$this->votes++;
			$this->_data['extra']['voted'][] = $user_id;
			$this->_set_changed('extra');
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Custom save method for the ticket
	 * so we can do what we need with the timeline and such.
	 *
	 * @return bool
	 */
	public function save()
	{
		if ($this->_is_new())
		{
			$this->ticket_id = $this->project->next_tid;
			$this->project->next_tid++;
			$this->project->save();
		}

		if (parent::save())
		{
			// Timeline entry
			$timeline = new Timeline(array(
				'project_id' => $this->project_id,
				'owner_id' => $this->id,
				'action' => 'ticket_created',
				'data' => $this->status_id || 1,
				'user_id' => $this->user_id
			));

			$timeline->save();

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Used to update the ticket properties.
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function update_data($data)
	{
		$to_values = array();
		$changes = array();

		// Loop over the data
		foreach ($data as $field => $value)
		{
			// Check if the value is different
			if ($this->_data[$field] == $value)
			{
				continue;
			}

			// Get the to and from values for different fields
			switch($field)
			{
				case 'assigned_to_id':
					$from = $this->assigned_to_id == 0 ? null : $this->assigned_to->username;
					$user = User::find($value);
					$to = $user ? $user->username : null;
				break;

				case 'status_id':
				case 'type_id':
					$class = 'Ticket' . ucfirst(str_replace('_id', '', $field));
					$to_values[$field] = $class::find($value);

					$from = $this->$class->name;
					$to = $to_values[$field]->name;
				break;

				case 'summary':
					$from = $this->summary;
					$to = $value;
				break;

				default:
					$class = str_replace('_id', '', $field);
					$to_values[$field] = $class::find($value);

					$from = $this->$class->name;
					$to = $to_values[$field]->name;
				break;
			}

			// One last value check...
			if ($from == $to)
			{
				continue;
			}

			// Change data
			$change = array(
				'property' => str_replace('_id', '', $field),
				'from' => $from,
				'to' => $to
			);

			// Has the status changed?
			if ($field == 'status_id' and $this->status_id != $value)
			{
				if ($this->status->status != $to_values[$field]->status)
				{
					$this->status = $to_status->status;
					$change['action'] = $to_status->status == 1 ? 'open' : 'close';
				}
			}

			// Set value
			$this->set($field, $value);
			$changes[] = $change;
		}

		// Any changes, or perhaps a comment?
		if (count($changes) > 0 or !empty(Request::$post['comment']))
		{
			$history = new TicketHistory(array(
				'user_id' => Avalon::app()->user->id,
				'ticket_id' => $this->id,
				'changes' => count($changes) > 0 ? json_encode($changes) : '',
				'comment' => isset(Request::$post['comment']) ? Request::$post['comment'] : ''
			));
			
			$history->save();
		}
		
		// Save
		return $this->save();
	}

	/**
	 * Checks if the models data is valid.
	 *
	 * @return bool
	 */
	public function is_valid()
	{
		$errors = array();

		if (empty($this->_data['summary']))
		{
			$errors['summary'] = l('errors.tickets.summary_blank');
		}

		if (empty($this->_data['body']))
		{
			$errors['body'] = l('errors.tickets.description_blank');
		}

		$this->errors = $errors;
		return !count($errors) > 0;
	}

	/**
	 * Returns the ticket data as an array.
	 *
	 * @param array $fields Fields to return
	 *
	 * @return array
	 */
	public function __toArray($fields = null)
	{
		$data = parent::__toArray($fields);

		// Extra data to fetch
		$relations = array(
			'project'     => array('id', 'name'),
			'user'        => array('id', 'username', 'name'),
			'assigned_to' => array('id', 'username', 'name'),
			'milestone'   => array('id', 'name'),
			'version'     => array('id', 'name'),
			'component'   => array('id', 'name'),
			'status'      => array('id', 'name'),
			'priority'    => array('id', 'name'),
			'severity'    => array('id', 'name'),
			'type'        => array('id', 'name'),
		);

		// Loop over the relations
		foreach ($relations as $name => $fields)
		{
			// Add the relation data and remove its ID
			// from the main array
			$data[$name] = $this->$name ? $this->$name->__toArray($fields) : null;
			unset($data[$name . '_id']);
		}

		return $data;
	}

	/**
	 * Processes the data when reading from the database.
	 *
	 * @access private
	 */
	protected function process_data_read()
	{
		$this->extra = json_decode(isset($this->_data['extra']) ? $this->_data['extra'] : '', true);
	}

	/**
	 * Processes the data when saving to the database.
	 *
	 * @access private
	 */
	protected function process_data_write()
	{
		if (isset($this->_data['extra']) and is_array($this->_data['extra']))
		{
			$this->extra = json_encode($this->_data['extra']);
		}
	}
}