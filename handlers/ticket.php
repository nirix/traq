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

addcrumb($uri->geturi(),l('ticket_x',$ticket['ticket_id']));

// Check if this is a private ticket,
// if it is, only let the admins, managers and registered owner see it.
if($ticket['private'] && (!$user->group['is_admin'] or ($user->info['id'] != $ticket['user_id'] && $user->loggedin)))
{
	include(template('no_permission'));
	exit;
}

// Update Ticket
if(isset($_POST['update']))
{
	$changes = array();
	
	// Type
	if($_POST['type'] != $ticket['type'])
	{
		$changes[] = array('property'=>'type','from'=>$ticket['type'],'to'=>$_POST['type']);
	}
	// Priority
	if($_POST['priority'] != $ticket['priority'])
	{
		$changes[] = array('property'=>'priority','from'=>$ticket['priority'],'to'=>$_POST['priority']);
	}
	// Milestone
	if($_POST['milestone'] != $ticket['milestone_id'])
	{
		$changes[] = array('property'=>'milestone','from'=>$ticket['milestone']['id'],'to'=>$_POST['milestone']);
	}
	// Component
	if($_POST['component'] != $ticket['component_id'])
	{
		$changes[] = array('property'=>'component','from'=>$ticket['component']['id'],'to'=>$_POST['component']);
	}
	// Assigned to
	if($_POST['assign_to'] != $ticket['assigned_to'])
	{
		$changes[] = array('property'=>'assigned_to','from'=>$ticket['assigned_to'],'to'=>$_POST['assign_to']);
	}
	// Severity
	if($_POST['severity'] != $ticket['severity'])
	{
		$changes[] = array('property'=>'severity','from'=>$ticket['severity'],'to'=>$_POST['severity']);
	}
	// Version
	if($_POST['version'] != $ticket['version_id'])
	{
		$changes[] = array('property'=>'version','from'=>$ticket['version_id'],'to'=>$_POST['version']);
	}
	// Summary
	if($_POST['summary'] != $ticket['summary'])
	{
		$changes[] = array('property'=>'summary','from'=>$ticket['summary'],'to'=>$_POST['summary']);
	}
	// Action
	if($_POST['action'] == 'mark' && $ticket['status'] != $_POST['mark_as'])
	{
		$changes[] = array('property'=>'status','from'=>$ticket['status'],'to'=>$_POST['mark_as'],'action'=>'mark');
	}
	elseif($_POST['action'] == 'close' && $ticket['status'] != $_POST['close_as'])
	{
		$changes[] = array('property'=>'status','from'=>$ticket['status'],'to'=>$_POST['close_as'],'action'=>'close');
	}
	elseif($_POST['action'] == 'reopen' && $ticket['status'] != $_POST['reopen_as'])
	{
		$changes[] = array('property'=>'status','from'=>$ticket['status'],'to'=>$_POST['reopen_as'],'action'=>'reopen');
	}
	print_r($changes);
	exit;
	echo "INSERT INTO ".DBPF."ticket_history VALUES(
		0,
		'".$user->info['id']."',
		'".$user->info['username']."',
		'".time()."',
		'".$ticket['id']."',
		'".json_encode($changes)."',
		'".$db->res($_POST['comment'])."'
	)";
}

// Assuming all goes well, display the view ticket page.
include(template('view_ticket'));
?>