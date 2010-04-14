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

include(TRAQPATH.'inc/ticket.class.php'); // Fetch the ticket class
$ticket = new Ticket;

addcrumb($uri->geturi(),l('timeline'));

// Set the limit
$limit = ($_REQUEST['days'] ? $_REQUEST['days'] : 7);

// Fetch timeline
$days = array();
$fetchdays = $db->query("SELECT DISTINCT YEAR(date) AS 'year', MONTH(date) AS 'month', DAY(date) AS 'day', date, timestamp FROM ".DBPF."timeline WHERE project_id='".$project['id']."' GROUP BY YEAR(date), MONTH(date), DAY(date) ORDER BY date DESC ".iif(is_numeric($limit),"LIMIT ".$db->res($limit)));
while($dayinfo = $db->fetcharray($fetchdays))
{
	// Set the day information
	$day = array();
	$day['date'] = $dayinfo['date'];
	$day['timestamp'] = $dayinfo['timestamp'];
	$day['changes'] = array();
	
	// Fetch changes
	$fetchchanges = $db->query("SELECT * FROM ".DBPF."timeline WHERE project_id='".$project['id']."' AND date='".$dayinfo['date']."' ORDER BY timestamp DESC");
	while($changeinfo = $db->fetcharray($fetchchanges))
	{
		// Only select ticket changes...
		if($changeinfo['action'] == 'open_ticket'
		or $changeinfo['action'] == 'close_ticket'
		or $changeinfo['action'] == 'reopen_ticket')
		{
			// Fetch ticket info
			$ticketinfo = $ticket->get(array('id'=>$changeinfo['owner_id']));
			
			// Set the changes
			$day['changes'][] = array(
				'text' => l('timeline_'.$changeinfo['action'],$changeinfo['user_name'],$ticketinfo['ticket_id'],$ticketinfo['summary']),
				'timestamp' => $changeinfo['timestamp'],
				'url' => $uri->anchor($project['slug'],'ticket-'.$ticketinfo['ticket_id'])
			);
		}
	}
	
	$days[] = $day;
}

($hook = FishHook::hook('handler_timeline')) ? eval($hook) : false;

require(template('timeline'));
?>