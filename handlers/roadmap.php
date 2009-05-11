<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

// Roadmap Page

($hook = FishHook::hook('roadmap_start')) ? eval($hook) : false;

$milestones = array();
$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project=".$project['id']." ".(isset($_REQUEST['all']) ? '' : (isset($_REQUEST['completed']) ? "AND completed > 0" : "AND completed='0'"))." ORDER BY milestone ASC"); // Fetch the milestones
while($info = $db->fetcharray($fetchmilestones)) {
	// Get Ticket Info
	$info['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND milestoneid='".$info['id']."'")); // Count open tickets
	$info['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status <= 0 AND milestoneid='".$info['id']."'")); // Count closed tickets
	$info['tickets']['total'] = ($info['tickets']['open']+$info['tickets']['closed']); // Count total tickets
	$info['tickets']['percent']['closed'] = calculatepercent($info['tickets']['closed'],$info['tickets']['total']); // Calculate closed tickets percent
	$info['tickets']['percent']['open'] = calculatepercent($info['tickets']['open'],$info['tickets']['total']); // Calculate open tickets percent
	$info['desc'] = formattext($info['desc']);
	($hook = FishHook::hook('roadmap_fetchtickes')) ? eval($hook) : false;
	$milestones[] = $info;
}
unset($fetchmilestones,$info);
	
// Breadcrumbs
$breadcrumbs[$uri->anchor($project['slug'],'roadmap')] = l('roadmap');

($hook = FishHook::hook('roadmap_end')) ? eval($hook) : false;

include(template('roadmap')); // Fetch the roadmap template
?>