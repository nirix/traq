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

// Fetch the ticket class
include(TRAQPATH.'inc/ticket.class.php');
$ticketC = new Ticket;

// Include reCaptcha
require(TRAQPATH.'inc/recaptchalib.php');

// Get the ticket info.
$ticket = $ticketC->get(array('ticket_id'=>$matches['id'],'project_id'=>$project['id'])); // Fetch the ticket.
$ticket['extra_json'] = $ticket['extra'];
$ticket['extra'] = array();
foreach((array)json_decode($ticket['extra_json']) as $cfield_id => $cfield_value)
{
	$ticket['extra'][$cfield_id] = $cfield_value;
}

// Check if the ticket exists...
if(!$ticket['id'])
	die();

// Watch/Unwatch
if($uri->seg(2) == 'watch')
{
	if(is_subscribed('ticket',$ticket['id']))
		remove_subscription('ticket',$ticket['id']);
	else
		add_subscription('ticket',$ticket['id']);
	
	header("Location: ".$uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'].'?'.(is_subscribed('ticket',$ticket['id']) ? 'subscribed' : 'unsubscribed')));
}

// Delete Comment
if(isset($_POST['action']) && $_POST['action'] == 'delete_comment')
{
	// Make sure the user is an Admin or Project Manager.
	if($user->group['is_admin'] or in_array($user->info['id'],$project['managers']))
	{
		$db->query("DELETE FROM ".DBPF."ticket_history WHERE id='".$db->res($_POST['comment'])."' LIMIT 1");
		header("Location: ".$uri->geturi());
	}
}

// Delete attachment
if(isset($_POST['action']) && $_POST['action'] == 'delete_attachment')
{
	$db->query("DELETE FROM ".DBPF."attachments WHERE id='".$db->res($_POST['attach_id'])."' LIMIT 1");
	header("Location: ".$uri->geturi());
}

// Delete ticket
if($uri->seg(2) == 'delete')
{
	// Make sure the user is an Admin or Project Manager.
	if($user->group['is_admin'] or in_array($user->info['id'],$project['managers']))
	{
		// Delete ticket
		$ticketC->delete($ticket['id']);
		header("Location: ".$uri->anchor($project['slug'],'tickets'));
	}
}

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
addcrumb($uri->anchor($project['slug'],'tickets'),l('tickets'));
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
	
	// Check for errors
	$errors = array();
	// Check reCaptcha
	if(!$user->loggedin and settings('recaptcha_enabled'))
	{
		$resp = recaptcha_check_answer(settings('recaptcha_privkey'),$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		
		if(!$resp->is_valid) {
			$recaptcha_error = $resp->error;
			$errors['recaptcha'] = l('error_recaptcha');
		}
	}
	// Check guest name
	if(empty($_POST['name']) && !$user->loggedin)
		$errors['name'] = l('error_name_empty');
	
	// Set the private field if its not set...
	if(empty($_POST['private']))
		$_POST['private'] = $ticket['private'];
	
	if(!count($errors))
	{
		if(isset($_FILES['file']) and $_FILES['file']['name'] != '') {
			$db->query("INSERT INTO ".DBPF."attachments
				(name,contents,type,size,uploaded,owner_id,owner_name,ticket_id,project_id)
				VALUES(
				'".$db->res($_FILES['file']['name'])."',
				'".base64_encode(file_get_contents($_FILES['file']['tmp_name']))."',
				'".$db->res($_FILES['file']['type'])."',
				'".$db->res($_FILES['file']['size'])."',
				'".time()."',
				'".$user->info['id']."',
				'".(isset($_COOKIE['guestname']) ? $_COOKIE['guestname'] : $user->info['username'])."',
				'".$ticket['id']."',
				'".$project['id']."'
			)");
			
			$changes[] = array('property'=>'attachment','from'=>'','to'=>$_FILES['file']['name'],'action'=>'add');
		}
		
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
		if((int)$_POST['milestone'] != $ticket['milestone_id'])
		{
			$querybits[] = "milestone_id='".$db->res($_POST['milestone'])."'";
			$newmilestone = $db->fetcharray($db->query("SELECT milestone FROM ".DBPF."milestones WHERE id='".$db->res($_POST['milestone'])."' LIMIT 1"));
			$changes[] = array('property'=>'milestone','from'=>$ticket['milestone']['milestone'],'to'=>$newmilestone['milestone']);
		}
		// Component
		if((int)$_POST['component'] != $ticket['component_id'])
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
			$newversion = $db->fetcharray($db->query("SELECT milestone FROM ".DBPF."milestones WHERE id='".$db->res($_POST['version'])."' LIMIT 1"));
			$changes[] = array('property'=>'version','from'=>$ticket['version']['milestone'],'to'=>$newversion['milestone']);
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
			$changes[] = array('property'=>'private','from'=>$ticket['private'],'to'=>$_POST['private'],'action'=>($_POST['private'] ? 'private' : 'public'));
		}
		// Action
		if(isset($_POST['action']) && $_POST['action'] == 'mark' && $ticket['status'] != $_POST['mark_as'])
		{
			$querybits[] = "status='".$db->res($_POST['mark_as'])."'";
			$changes[] = array('property'=>'status','from'=>ticket_status($ticket['status']),'to'=>ticket_status($_POST['mark_as']),'action'=>'mark');
		}
		elseif(isset($_POST['action']) && $_POST['action'] == 'close' && $ticket['status'] != $_POST['close_as'])
		{
			$querybits[] = "status='".$db->res($_POST['close_as'])."'";
			$querybits[] = "closed='1'";
			$changes[] = array('property'=>'status','from'=>ticket_status($ticket['status']),'to'=>ticket_status($_POST['close_as']),'action'=>'close');
			
			// Insert timeline row
			$db->query("INSERT INTO ".DBPF."timeline (project_id,owner_id,action,data,user_id,user_name,timestamp,date) VALUES(
				'".$db->res($project['id'])."',
				'".$db->res($ticket['id'])."',
				'close_ticket',
				'".$ticket['ticket_id']."',
				'".$user->info['id']."',
				'".$db->res(($user->loggedin ? $user->info['username'] : $_POST['name']))."',
				'".time()."',
				NOW()
			)");
		}
		elseif(isset($_POST['action']) && $_POST['action'] == 'reopen' && $ticket['status'] != $_POST['reopen_as'])
		{
			$querybits[] = "status='".$db->res($_POST['reopen_as'])."'";
			$querybits[] = "closed='0'";
			$changes[] = array('property'=>'status','from'=>ticket_status($ticket['status']),'to'=>ticket_status($_POST['reopen_as']),'action'=>'reopen');
			
			// Insert timeline row
			$db->query("INSERT INTO ".DBPF."timeline (project_id,owner_id,action,data,user_id,user_name,timestamp,date) VALUES(
				'".$db->res($project['id'])."',
				'".$db->res($ticket['id'])."',
				'reopen_ticket',
				'".$ticket['ticket_id']."',
				'".$user->info['id']."',
				'".$db->res(($user->loggedin ? $user->info['username'] : $_POST['name']))."',
				'".time()."',
				NOW()
			)");
		}
		
		// Custom fields
		foreach($_POST['cfields'] as $cf_id => $cf_val)
		{
			if($cf_val != $ticket['extra'][$cf_id])
			{
				$changes[] = array('property'=>'custom_field','name'=>custom_field_name($cf_id),'from'=>$ticket['extra'][$cf_id],'to'=>$cf_val);
			}
		}
		
		$querybits[] = "extra='".$db->res(json_encode($_POST['cfields']))."'";
		
		($hook = FishHook::hook('ticket_update')) ? eval($hook) : false;
	
		if(count($changes) > 0 || $_POST['comment'] != '')
		{
			// Update the ticket
			if(count($querybits) > 0)
				$db->query("UPDATE ".DBPF."tickets SET ".implode(', ',$querybits)." WHERE id='".$ticket['id']."' LIMIT 1");
			
			// Set guest name cookie
			if(!$user->loggedin)
				setcookie('guestname',$_POST['name'],time()+50000,'/');
			
			// Insert row into ticket history
			$db->query("INSERT INTO ".DBPF."ticket_history (user_id,user_name,timestamp,ticket_id,project_id,changes,comment) VALUES(
				'".$user->info['id']."',
				'".($user->loggedin ? $user->info['username'] : $_POST['name'])."',
				'".time()."',
				'".$ticket['id']."',
				'".$project['id']."',
				'".$db->res(json_encode($changes))."',
				'".$db->res($_POST['comment'])."'
			)");
			
			// Update ticket updated field
			$db->query("UPDATE ".DBPF."tickets SET updated='".time()."' WHERE id='".$ticket['id']."' LIMIT 1");
			
			// Send notification
			$notification = array(
				'type' => 'ticket_updated',
				'url' => 'http://'.$_SERVER['HTTP_HOST'].$uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id']),
				'id' => $ticket['id'],
				'tid' => $ticket['ticket_id'],
				'summary' => $ticket['summary']
			);
			send_notification('ticket',$notification);
			
			header("Location: ".$uri->geturi().'?updated');
		}
	}
}

// Ticket Properties
$ticket_properties = array(
	'reported_by' => array('label'=>l('reported_by'),'value'=>$ticket['user_name']),
	'assigned_to' => array('label'=>l('assigned_to'),'value'=>$ticket['assignee']['name']),
	'type' => array('label'=>l('type'),'value'=>ticket_type($ticket['type'])),
	'priority' => array('label'=>l('priority'),'value'=>ticket_priority($ticket['priority'])),
	'severity' => array('label'=>l('severity'),'value'=>ticket_severity($ticket['severity'])),
	'component' => array('label'=>l('component'),'value'=>$ticket['component']['name']),
	'milestone' => array('label'=>l('milestone'),'value'=>$ticket['milestone']['milestone']),
	'version' => array('label'=>l('version'),'value'=>$ticket['version']['milestone']),
	'status' => array('label'=>l('status'),'value'=>ticket_status($ticket['status'])),
);

foreach(custom_fields() as $field) {
	$ticket_properties[$field['id']] = array('label'=>$field['name'],'value'=>$ticket['extra'][$field['id']]);
}

($hook = FishHook::hook('handler_ticket')) ? eval($hook) : false;

// Assuming all goes well, display the view ticket page.
include(template('view_ticket'));
?>