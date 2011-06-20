<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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
 * @param string $column The column to get the content for.
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
 * @param string $column The column to get the content for.
 * @param object $ticket The ticket data object.
 * @return mixed
 */
function ticketlist_data($column, $ticket) {
	switch ($column) {
		case 'ticket_id':
			return $ticket->ticket_id;
		case 'summary':
			return $ticket->summary;
		case 'status':
			return $ticket->status->name;
		case 'owner':
			return $ticket->user->username;
		case 'milestone':
			return $ticket->milestone->milestone;
		default:
			return '';
		break;
	}
}