<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
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
 * Returns the content for the ticket listing headers.
 *
 * @param string $column The column to get the content for.
 *
 * @return mixed
 */
function ticketlist_header($column) {
	switch ($column) {
		case 'ticket_id':
			return '';
		case 'summary':
		case 'status':
		case 'owner':
		case 'type':
		case 'component':
		case 'milestone':
			return l($column);
		default:
			return '';
		break;
	}
}

/**
 * Returns the content for the ticket listing field.
 *
 * @param string $column The column to get the content for.
 * @param object $ticket The ticket data object.
 *
 * @return mixed
 */
function ticketlist_data($column, $ticket) {
	switch ($column)
	{
		// Ticket ID column
		case 'ticket_id':
			return $ticket->ticket_id;
		
		// Summary column
		case 'summary':
			return $ticket->summary;
		
		// Status column
		case 'status':
			return $ticket->status->name;
		
		// Owner / author column
		case 'owner':
			return $ticket->user->username;
		
		// Ticket type column
		case 'type':
			return $ticket->type->name;
		
		// Component column
		case 'component':
			return $ticket->component ? $ticket->component->name : '';
		
		// Milestone column
		case 'milestone':
			return $ticket->milestone ? $ticket->milestone->name : '';
		
		// Unknown column...
		default:
			return '';
		break;
	}
}