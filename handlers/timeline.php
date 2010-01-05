<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

include(TRAQPATH.'inc/ticket.class.php'); // Fetch the ticket class
$ticket = new Ticket;

addcrumb($uri->geturi(),l('timeline'));

// Fetch timeline
$days = array();
$fetchdays = $db->query("SELECT DISTINCT YEAR(date) AS 'year', MONTH(date) AS 'month', DAY(date) AS 'day', date, timestamp FROM ".DBPF."timeline WHERE project_id='".$project['id']."' GROUP BY YEAR(date), MONTH(date), DAY(date) ORDER BY date DESC");
while($dayinfo = $db->fetcharray($fetchdays))
{
	$day = array();
	$day['date'] = $dayinfo['date'];
	$day['timestamp'] = $dayinfo['timestamp'];
	$day['changes'] = array();
	
	// Fetch changes
	$fetchchanges = $db->query("SELECT * FROM ".DBPF."timeline WHERE project_id='".$project['id']."' AND date='".$dayinfo['date']."' ORDER BY timestamp DESC");
	while($changeinfo = $db->fetcharray($fetchchanges))
	{
		if($changeinfo['action'] == 'open_ticket'
		or $changeinfo['action'] == 'close_ticket'
		or $changeinfo['action'] == 'reopen_ticket')
		{
			$ticketinfo = $ticket->get(array('id'=>$changeinfo['owner_id']));
			$day['changes'][] = array(
				'text' => l('timeline_'.$changeinfo['action'],$changeinfo['user_name'],$ticketinfo['ticket_id'],$ticketinfo['summary']),
				'timestamp' => $changeinfo['timestamp'],
				'url' => $uri->anchor($project['slug'],'ticket-'.$ticketinfo['ticket_id'])
			);
		}
	}
	
	$days[] = $day;
}

require(template('timeline'));
?>