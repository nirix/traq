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
		'extra',
		'created_at',
		'updated_at'
	);
	
	protected static $_belongs_to = array(
		'user', 'project', 'milestone', 'component', 'priority',
		'assigned_to' => array('model' => 'user'),
		'version'     => array('model' => 'milestone'),
		'status'      => array('model' => 'ticketstatus'),
		'type'        => array('model' => 'tickettype')
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
	 * Custom save method for the ticket
	 * so we can do what we need with the timeline and such.
	 *
	 * @return bool
	 */
	public function save()
	{
		if ($parent->save())
		{
			// code here to insert into timeline and such..
			
			return true;
		}
		else
		{
			return false;
		}
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
			'priority'    => null,
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
}