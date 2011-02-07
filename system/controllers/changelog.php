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

addcrumb($uri->geturi(),l('changelog'));

// Fetch Ticket Types
$types = array();
$fetchtickettypes = $db->query("SELECT * FROM ".DBPF."ticket_types");
while($info = $db->fetcharray($fetchtickettypes))
	$types[$info['id']] = $info;

// Fetch Ticket Statuses
$statuses = array();
$fetchtickettypes = $db->query("SELECT * FROM ".DBPF."ticket_status");
while($info = $db->fetcharray($fetchtickettypes))
	$statuses[$info['id']] = $info;

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
		if($types[$change['type']]['changelog'] && $statuses[$change['status']]['changelog'])
			$info['changes'][] = $change;
	
	$milestones[] = $info;
}

($hook = FishHook::hook('handler_changelog')) ? eval($hook) : false;

include(template('changelog'));
?>