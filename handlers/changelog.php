<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

addcrumb($uri->geturi(),l('changelog'));

// Fetch Ticket Types
$types = array();
$fetchtickettypes = $db->query("SELECT * FROM ".DBPF."ticket_types");
while($info = $db->fetcharray($fetchtickettypes))
{
	$types[$info['id']] = $info;
}

// Fetch Milestones
$milestones = array();
$fetchmilestones = $db->query("SELECT id,milestone,completed,changelog FROM ".DBPF."milestones WHERE project_id='".$project['id']."' AND completed > 0 ORDER BY completed DESC");
while($info = $db->fetcharray($fetchmilestones))
{
	$info['changes'] = array();
	
	// Format the changelog text.
	$info['changelog'] = formattext($info['changelog']);
	
	// Fetch changes
	$fetchchanges = $db->query("SELECT id,summary,status,type FROM ".DBPF."tickets WHERE milestone_id='".$info['id']."' ORDER BY summary ASC");
	while($change = $db->fetcharray($fetchchanges))
	{
		$info['changes'][] = $change;
	}
	
	$milestones[] = $info;
}

include(template('changelog'));
?>