<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

$ticket = $db->queryfirst("SELECT * FROM ".DBPF."tickets WHERE ticket_id='".$matches['id']."' LIMIT 1");

if($ticket['private'] && (!$user->group['is_admin'] or ($user->info['id'] != $ticket['user_id'] && $user->loggedin)))
{
	include(template('no_permission'));
	exit;
}

include(template('view_ticket'));
?>