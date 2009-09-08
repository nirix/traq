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
$ticket = $ticket->get(array('ticket_id'=>$matches['id'],'project_id'=>$project['id'])); // Fetch the ticket.

// Check if this is a private ticket,
// if it is, only let the admins, managers and registered owner see it.
if($ticket['private'] && (!$user->group['is_admin'] or ($user->info['id'] != $ticket['user_id'] && $user->loggedin)))
{
	include(template('no_permission'));
	exit;
}

// Assuming all goes well, display the view ticket page.
include(template('view_ticket'));
?>