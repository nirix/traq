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

// Work out what milestones to display
if(isset($_REQUEST['all']))
	$filter = '';
elseif(isset($_REQUEST['completed']))
	$filter = "AND locked='1' AND completed > 0";
else
	$filter = "AND locked='0'";


// Fetch project milestones...
$milestones = array();
$fetch = $db->query("SELECT * FROM ".DBPF."milestones WHERE project_id='".$db->es($project['id'])."' $filter ORDER BY displayorder ASC");
while($info = $db->fetcharray($fetch))
{
	// Get the milestone tickets
	$info['tickets'] = array();
	$info['tickets']['open'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$info['id']."' AND project_id='".$project['id']."' AND closed='0'")); // Count open tickets
	$info['tickets']['closed'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$info['id']."' AND project_id='".$project['id']."' AND closed='1'")); // Count closed tickets
	$info['tickets']['total'] = ($info['tickets']['open']+$info['tickets']['closed']); // Count total tickets
	// Calculate percent
	$info['tickets']['percent'] = array(
		'open' => ($info['tickets']['open'] ? getpercent($info['tickets']['open'],$info['tickets']['total']) : 0),
		'closed' => getpercent($info['tickets']['closed'],$info['tickets']['total'])
	);
	($hook = FishHook::hook('roadmap_fetch')) ? eval($hook) : false;
	$milestones[] = $info;
}

addcrumb($uri->geturi(),l('roadmap'));

($hook = FishHook::hook('handler_roadmap')) ? eval($hook) : false;

require(template('roadmap'));
?>