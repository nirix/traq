<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
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
 *
 * $Id$
 */

require(TRAQPATH.'inc/rss.class.php');
require(TRAQPATH.'inc/ticket.class.php');

$ticket = new Ticket;

// Timeline Feed
if($uri->seg[2] == 'timeline')
{
	// Fetch changes
	$items = array();
	$fetchchanges = $db->query("SELECT * FROM ".DBPF."timeline WHERE project_id='".$project['id']."' ORDER BY timestamp DESC LIMIT 20");
	while($changeinfo = $db->fetcharray($fetchchanges))
	{
		// Only select ticket changes...
		if($changeinfo['action'] == 'open_ticket'
		or $changeinfo['action'] == 'close_ticket'
		or $changeinfo['action'] == 'reopen_ticket')
		{
			// Fetch ticket info
			$ticketinfo = $ticket->get(array('id'=>$changeinfo['owner_id']));
			
			$items[] = array(
				'title' => l('timeline_'.$changeinfo['action'],$changeinfo['user_name'],$ticketinfo['ticket_id'],$ticketinfo['summary']),
				'content' => l('timeline_'.$changeinfo['action'],$changeinfo['user_name'],$ticketinfo['ticket_id'],$ticketinfo['summary']),
				'content_encoded' => l('timeline_'.$changeinfo['action'],$changeinfo['user_name'],$ticketinfo['ticket_id'],$ticketinfo['summary']),
				'timestamp' => $changeinfo['timestamp'],
				'link' => 'http://'.$_SERVER['HTTP_HOST'].$uri->anchor($project['slug'],'ticket-'.$ticketinfo['ticket_id']),
				'guid' => 'http://'.$_SERVER['HTTP_HOST'].$uri->anchor($project['slug'],'ticket-'.$ticketinfo['ticket_id'])
			);
		}
	}
	
	$timeline = new RSSFeed(l('x_timeline',$project['name']),'http://'.$_SERVER['HTTP_HOST'].$uri->anchor($projcet['slug'],'feeds','timeline'),l('x_timeline',$project['name']),$items);
	$timeline->output();
}