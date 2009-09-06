<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

$ticket = $db->queryfirst("SELECT * FROM ".DBPF."tickets WHERE ticket_id='".$matches['id']."' LIMIT 1"); // Fetch the ticket info
$milestone = $db->queryfirst("SELECT * FROM ".DBPF."milestones WHERE id='".$db->res($ticket['milestone_id'])."' LIMIT 1"); // Fetch the milestone info
$version = $db->queryfirst("SELECT * FROM ".DBPF."versions WHERE id='".$db->res($ticket['version_id'])."' LIMIT 1"); // Fetch the version info
$component = $db->queryfirst("SELECT * FROM ".DBPF."components WHERE id='".$db->res($ticket['component_id'])."' LIMIT 1"); // Fetch the component info

// For now, just make the attachments an empty array,
// this hides the errors.
$attachments = array();

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