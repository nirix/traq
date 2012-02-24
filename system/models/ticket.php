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
		'assignee_id',
		'is_closed',
		'is_private',
		'extra',
		'created_at',
		'updated_at'
	);
	
	protected static $_belongs_to = array(
		'user', 'project', 'milestone', 'component',
		'status' => array('model' => 'ticketstatus'),
		'type' => array('model' => 'tickettype'),
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
}