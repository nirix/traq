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

class Tickets
{
	public static function column_header($name, $before='<th>', $after='</th>')
	{
		$col = $before;
		switch($name)
		{
			case 'ticket_id':
				$col .= l('ticket');
			break;
			
			default:
				$col .= l($name);
			break;
		}
		$col .= $after;
		
		return $col;
	}

	public static function column_content($name, $ticket, $before='<td>', $after='</td>')
	{
		$project = Meridian::app()->project;
		
		$col = $before;
		switch($name)
		{
			case 'id':
			case 'ticket_id':
				$col .= $ticket['ticket_id'];
			break;
			case 'summary':
				$col .= HTML::link(baseurl($project['slug'], 'tickets', $ticket['ticket_id']), $ticket['summary']);
			break;
			case 'status':
			case 'priority':
			case 'type':
			case 'severity':
			case 'component':
				$col .= @$ticket[$name]['name'];
			break;
			case 'milestone':
				$col .= @$ticket['milestone']['milestone'];
			break;
			case 'version':
				$col .= @$ticket['version']['milestone'];
			break;
			case 'owner':
			case 'assigned_to':
				$col .= (isset($ticket[$name]['id']) ? HTML::link(baseurl('users', $ticket[$name]['id']), $ticket[$name]['username']) : '');
			break;
			case 'updated':
				$col .= l('x_ago', timesince($ticket['updated'] > 0 ? $ticket['updated'] : $ticket['created']));
			break;
			default:
				$col .= @$ticket[$name];
			break;
		}
		$col .= $after;
		
		return $col;
	}
}