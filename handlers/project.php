<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Get the project info
$project = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($uri->seg[0])."' LIMIT 1"));

// Check what page to display
if(!isset($uri->seg[1])) {
	// Project Info page
	include(template('project'));
} elseif($uri->seg[1] == "roadmap") {
	// Roadmap Page
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project=".$project['id']." ORDER BY due ASC");
	while($info = $db->fetcharray($fetchmilestones)) {
		// Get Ticket Info
		$info['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND milestoneid='".$info['id']."'"));
		$info['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status='0' AND milestoneid='".$info['id']."'"));
		$info['tickets']['total'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE milestoneid='".$info['id']."'"));
		$info['tickets']['percent']['closed'] = calculatepercent($info['tickets']['closed'],$info['tickets']['total']);
		$info['tickets']['percent']['open'] = calculatepercent($info['tickets']['open'],$info['tickets']['total']);
		$milestones[] = $info;
	}
	unset($fetchmilestones,$info);
	include(template('roadmap'));
} elseif($uri->seg[1] == "tickets") {
	// Tickets Page
	$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1"));
	$tickets = array();
	if($uri->seg[3] == "open") {
		$status = "status >= 1";
	} elseif($uri->seg[3] == "closed") {
		$status = "status='0'";
	}
	// Get Tickets
	$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE $status AND milestoneid='".$milestone['id']."' AND projectid='".$project['id']."' ORDER BY priority DESC");
	while($info = $db->fetcharray($fetchtickets)) {
		$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
		$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
		$tickets[] = $info;
	}
	unset($fetchtickets,$info);
	include(template('tickets'));
} else if($uri->seg[1] == "newticket") {
	include(template('newticket'));
} else if($uri->seg[1] == "ticket") {
	$ticket = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE id='".$db->escapestring($uri->seg[2])."' AND projectid='".$project['id']."' LIMIT 1")); // Get Ticket info
	if($_POST['action'] == "update") {
		$changes = array();
		if($_POST['type'] != $ticket['type']) {
			$changes[] = "TYPE:".$_POST['type'].",".$ticket['type'];
		}
		if($_POST['assignto'] != $ticket['assigneeid']) {
			$changes[] = "ASIGNEE:".$_POST['assignto'].",".$ticket['assigneeid'];
		}
		if($_POST['priority'] != $ticket['priority']) {
			$changes[] = "PRIORITY:".$_POST['priority'].",".$ticket['priority'];
		}
		if($_POST['severity'] != $ticket['severity']) {
			$changes[] = "SEVERITY:".$_POST['severity'].",".$ticket['severity'];
		}
		if($_POST['milestone'] != $ticket['milestoneid']) {
			$changes[] = "MILESTONE:".$_POST['milestone'].",".$ticket['milestoneid'];
		}
		if($_POST['version'] != $ticket['versionid']) {
			$changes[] = "VERSION:".$_POST['version'].",".$ticket['versionid'];
		}
		if($_POST['component'] != $ticket['componentid']) {
			$changes[] = "COMPONENT:".$_POST['component'].",".$ticket['componentid'];
		}
		if($_POST['close']) {
			$changes[] = "CLOSE";
		}
		$changes = implode('|',$changes);
		$db->query("UPDATE ".DBPREFIX."tickets SET type='".$db->escapestring($_POST['type'])."',
				   								   assigneeid='".$db->escapestring($_POST['assignto'])."',
												   priority='".$db->escapestring($_POST['priority'])."',
												   severity='".$db->escapestring($_POST['severity'])."',
												   milestoneid='".$db->escapestring($_POST['milestone'])."',
												   versionid='".$db->escapestring($_POST['version'])."',
												   componentid='".$db->escapestring($_POST['component'])."',
												   updated='".time()."'
												   WHERE id='".$ticket['id']."' LIMIT 1");
		$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->uid.",".$ticket['id'].",'".$changes."')");
		header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['id'],'?updated'));
		print_r($changes);
	} else {
		// View Ticket
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$ticket['milestoneid']."' LIMIT 1")); // Get ticket Milestone info
		$version = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$ticket['versionid']."' LIMIT 1")); // Get ticket Version info
		$component = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$ticket['componentid']."' LIMIT 1")); // Get ticket Component info
		$owner = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$ticket['ownerid']."' LIMIT 1")); // Get ticket Owner info
		$assignee = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$ticket['assigneeid']."' LIMIT 1")); // Get ticket Assignee info
		// Ticket History
		$history = array();
		$gethistory = $db->query("SELECT * FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."' ORDER BY id ASC");
		while($info = $db->fetcharray($gethistory)) {
			$info['user'] = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$info['userid']."' LIMIT 1"));
			$changes = explode('|',$info['changes']);
			$info['changes'] = array();
			foreach($changes as $change) {
				$parts = explode(':',$change);
				$type = $parts[0];
				$values = explode(',',$parts[1]);
				$change = array();
				$change['type'] = $type;
				$change['val1'] = $values[0];
				$change['val2'] = $values[1];
				$change['val3'] = $values[2];
				$change['val4'] = $values[3];
				$info['changes'][] = $change;
			}
			$history[] = $info;
		}
		include(template('ticket'));
	}
}
?>