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
	$ticket = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE id='".$db->escapestring($uri->seg[2])."' AND projectid='".$project['id']."' LIMIT 1"));
	$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$ticket['milestoneid']."' LIMIT 1"));
	$version = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$ticket['versionid']."' LIMIT 1"));
	$component = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$ticket['componentid']."' LIMIT 1"));
	$owner = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$ticket['ownerid']."' LIMIT 1"));
	$assignee = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$ticket['assigneeid']."' LIMIT 1"));
	include(template('ticket'));
}
?>