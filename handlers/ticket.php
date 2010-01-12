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

// Fetch ticket histoy
$ticket['changes'] = array();
$fetchchanges = $db->query("SELECT * FROM ".DBPF."ticket_history WHERE ticket_id='".$ticket['id']."' ORDER BY id DESC");
while($changeinfo = $db->fetcharray($fetchchanges))
{
	$changeinfo['changes'] = json_decode($changeinfo['changes']);
	$ticket['changes'][] = $changeinfo;
}
unset($fetchchanges,$changeinfo);

// Add crumbs
addcrumb($uri->anchor(PROJECT_SLUG,'tickets'),l('tickets'));
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
	$querybits = array();
	
	// Type
	if($_POST['type'] != $ticket['type'])
	{
		$querybits[] = "type='".$db->res($_POST['type'])."'";
		$changes[] = array('property'=>'type','from'=>ticket_type($ticket['type']),'to'=>ticket_type($_POST['type']));
	}
	// Priority
	if($_POST['priority'] != $ticket['priority'])
	{
		$querybits[] = "priority='".$db->res($_POST['priority'])."'";
		$changes[] = array('property'=>'priority','from'=>ticket_priority($ticket['priority']),'to'=>ticket_priority($_POST['priority']));
	}
	// Milestone
	if($_POST['milestone'] != $ticket['milestone_id'])
	{
		$querybits[] = "milestone_id='".$db->res($_POST['milestone'])."'";
		$newmilestone = $db->fetcharray($db->query("SELECT milestone FROM ".DBPF."milestones WHERE id='".$db->res($_POST['milestone'])."' LIMIT 1"));
		$changes[] = array('property'=>'milestone','from'=>$ticket['milestone']['milestone'],'to'=>$newmilestone['milestone']);
	}
	// Component
	if($_POST['component'] != $ticket['component_id'])
	{
		$querybits[] = "component_id='".$db->res($_POST['component'])."'";
		$newcomponent = $db->fetcharray($db->query("SELECT name FROM ".DBPF."components WHERE id='".$db->res($_POST['component'])."' LIMIT 1"));
		$changes[] = array('property'=>'component','from'=>$ticket['component']['name'],'to'=>$newcomponent['name']);
	}
	// Assigned to
	if($_POST['assign_to'] != $ticket['assigned_to'])
	{
		$querybits[] = "assigned_to='".$db->res($_POST['assign_to'])."'";
		$newassignee = $db->fetcharray($db->query("SELECT username FROM ".DBPF."users WHERE id='".$db->res($_POST['assign_to'])."' LIMIT 1"));
		$changes[] = array('property'=>'assigned_to','from'=>$ticket['assignee']['username'],'to'=>$newassignee['username']);
	}
	// Severity
	if($_POST['severity'] != $ticket['severity'])
	{
		$querybits[] = "severity='".$db->res($_POST['severity'])."'";
		$changes[] = array('property'=>'severity','from'=>ticket_severity($ticket['severity']),'to'=>ticket_severity($_POST['severity']));
	}
	// Version
	if($_POST['version'] != $ticket['version_id'])
	{
		$querybits[] = "version_id='".$db->res($_POST['version'])."'";
		$newversion = $db->fetcharray($db->query("SELECT version FROM ".DBPF."versions WHERE id='".$db->res($_POST['version'])."' LIMIT 1"));
		$changes[] = array('property'=>'version','from'=>$ticket['version']['version'],'to'=>$newversion['version']);
	}
	// Summary
	if($_POST['summary'] != $ticket['summary'])
	{
		$querybits[] = "summary='".$db->res($_POST['summary'])."'";
		$changes[] = array('property'=>'summary','from'=>$ticket['summary'],'to'=>$_POST['summary']);
	}
	// Private ticket
	elseif($_POST['private'] != $ticket['private'])
	{
		$querybits[] = "private='".$db->res($_POST['private'])."'";
	}
	// Action
	if($_POST['action'] == 'mark' && $ticket['status'] != $_POST['mark_as'])
	{
		$querybits[] = "status='".$db->res($_POST['mark_as'])."'";
		$changes[] = array('property'=>'status','from'=>ticket_status($ticket['status']),'to'=>ticket_status($_POST['mark_as']),'action'=>'mark');
	}
	elseif($_POST['action'] == 'close' && $ticket['status'] != $_POST['close_as'])
	{
		$querybits[] = "status='".$db->res($_POST['close_as'])."'";
		$querybits[] = "closed='1'";
		$changes[] = array('property'=>'status','from'=>ticket_status($ticket['status']),'to'=>ticket_status($_POST['close_as']),'action'=>'close');
		$db->query("INSERT INTO ".DBPF."timeline VALUES(
			0,
			'".$db->res($project['id'])."',
			'".$db->res($ticket['id'])."',
			'close_ticket',
			'".$ticket['ticket_id']."',
			'".$user->info['id']."',
			'".$db->res($user->info['username'])."',
			'".time()."',
			NOW()
		)");
	}
	elseif($_POST['action'] == 'reopen' && $ticket['status'] != $_POST['reopen_as'])
	{
		$querybits[] = "status='".$db->res($_POST['reopen_as'])."'";
		$querybits[] = "closed='0'";
		$changes[] = array('property'=>'status','from'=>ticket_status($ticket['status']),'to'=>ticket_status($_POST['reopen_as']),'action'=>'reopen');
		$db->query("INSERT INTO ".DBPF."timeline VALUES(
			0,
			'".$db->res($project['id'])."',
			'".$db->res($ticket['id'])."',
			'reopen_ticket',
			'".$ticket['ticket_id']."',
			'".$user->info['id']."',
			'".$db->res($user->info['username'])."',
			'".time()."',
			NOW()
		)");
	}
	
	($hook = FishHook::hook('ticket_update')) ? eval($hook) : false;
	
	if(count($changes) > 0 || $_POST['comment'] != '')
	{
		// Update the ticket
		if(count($changes) > 0)
			$db->query("UPDATE ".DBPF."tickets SET ".implode(', ',$querybits)." WHERE id='".$ticket['id']."' LIMIT 1");
		
		// Insert row into ticket history
		$db->query("INSERT INTO ".DBPF."ticket_history VALUES(
			0,
			'".$user->info['id']."',
			'".$user->info['username']."',
			'".time()."',
			'".$ticket['id']."',
			'".json_encode($changes)."',
			'".$db->res($_POST['comment'])."'
		)");
		header("Location: ".$uri->geturi().'?updated');
	}
}

// Assuming all goes well, display the view ticket page.
include(template('view_ticket'));
?>