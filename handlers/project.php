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
	include(template('project'));
} elseif($uri->seg[1] == "roadmap") {
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project=".$project['id']." ORDER BY due ASC");
	while($info = $db->fetcharray($fetchmilestones)) {
		$info['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND milestoneid='".$info['id']."'"));
		$info['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status='0' AND milestoneid='".$info['id']."'"));
		$info['tickets']['total'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE milestoneid='".$info['id']."'"));
		$info['tickets']['percent']['closed'] = ($info['tickets']['closed']/$info['tickets']['total']*100);
		$info['tickets']['percent']['open'] = ($info['tickets']['open']/$info['tickets']['total']*100);
		$milestones[] = $info;
	}
	unset($fetchmilestones,$info);
	include(template('roadmap'));
} elseif($uri->seg[1] == "tickets") {
	$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1"));
	$tickets = array();
	if($uri->seg[3] == "open") {
		$status = "status >= 1";
	} elseif($uri->seg[3] == "closed") {
		$status = "status='0'";
	}
	$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE $status AND milestoneid='".$milestone['id']."' ORDER BY priority DESC");
	while($info = $db->fetcharray($fetchtickets)) {
		$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1"));
		$info['owner'] = $user->getinfo($info['ownerid']);
		$tickets[] = $info;
	}
	unset($fetchtickets,$info);
	include(template('tickets'));
}
?>