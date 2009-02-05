<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

/**
 * Template
 * Used to easily fetch templates.
 */
function template($template) {
	global $origin;
	return $origin->template->load($template);
}

/**
 * Build Title
 * Makes the page title
 */
function buildtitle($title = array()) {
	global $settings;
	if(!is_array($title)) {
		$oldtitle = $title;
		$title = array();
		$title[] = $oldtitle;
	}
	$title[] = $settings->title;
	$title[] = "Traq";
	return implode(" | ",$title);
}

/**
 * Is Project
 * Check if the supplied string is a project.
 */
function is_project($string) {
	global $db;
	return $db->numrows($db->query("SELECT slug FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($string)."' LIMIT 1"));
}

/**
 * Get Project Milestones
 */
function projectmilestones($projectid) {
	global $db;
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project=".$projectid." ORDER BY id ASC");
	while($info = $db->fetcharray($fetchmilestones)) {
		$milestones[] = $info;
	}
	return $milestones;
}

/**
 * Get Project Components
 */
function projectcomponents($projectid) {
	global $db;
	$components = array();
	$fetchcomponents = $db->query("SELECT * FROM ".DBPREFIX."components WHERE project=".$projectid." ORDER BY name ASC");
	while($info = $db->fetcharray($fetchcomponents)) {
		$components[] = $info;
	}
	return $components;
}

/**
 * Get Project Versions
 */
function projectversions($projectid) {
	global $db;
	$versions = array();
	$fetchversions = $db->query("SELECT * FROM ".DBPREFIX."versions WHERE projectid=".$projectid." ORDER BY version DESC");
	while($info = $db->fetcharray($fetchversions)) {
		$versions[] = $info;
	}
	return $versions;
}

/**
 * Get Project Managers
 */
function projectmanagers($projectid) {
	global $db,$user;
	$managers = array();
	$project = $db->fetcharray($db->query("SELECT id,managers FROM ".DBPREFIX."projects WHERE id='".$projectid."' LIMIT 1"));
	$projectmanagers = explode(',',$project['managers']);
	foreach($projectmanagers as $manager) {
		$managers[] = $user->getinfo($manager);
	}
	return $managers;
}

/**
 * Ticket Status
 * Gets the Ticket Status text.
 */
function ticketstatus($statusid) {
	$statusses = array(
					   -3 => 'Fixed',
					   -2 => 'Rejected',
					   -1 => 'Completed',
					   0 => 'Closed',
					   1 => 'New',
					   2 => 'Accepted',
					   3 => 'Reopened'
					   );
	return $statusses[$statusid];
}

/**
 * Ticket Priority
 * Gets the Ticket Priorty text.
 */
function ticketpriority($priorityid) {
	$priorities = array(
					   1 => 'Lowest',
					   2 => 'Low',
					   3 => 'Normal',
					   4 => 'High',
					   5 => 'Highest'
					   );
	return $priorities[$priorityid];
}

/**
 * Ticket Type
 * Gets the Ticket Type text.
 */
function tickettype($typeid) {
	$types = array(
				   1 => 'Defect',
				   2 => 'Enhancement',
				   3 => 'Feature Request',
				   4 => 'Task'
				   );
	return $types[$typeid];
}

function ticketseverity($severityid) {
	$severity = array(
				   1 => 'Blocker',
				   2 => 'Critical',
				   3 => 'Major',
				   4 => 'Normal',
				   5 => 'Minor',
				   6 => 'Trivial'
				   );
	return $severity[$severityid];
}

/**
 * Calcuate Percent
 * Used to calculate the percent of two numbers,
 * if both numbers are the same, 100(%) is returned.
 */
function calculatepercent($min,$max) {
	if($min == $max) {
		return 100;
	}
	$calculate = ($min/$max*100);
	$split = explode('.',$calculate);
	return $split[0];
}

/**
 * Time Since
 */
function timesince($original, $detailed = 0) {
	$now = time();
	
	$chunks = array(
		array(60 * 60 * 24 * 365, 'year', 'years'),
		array(60 * 60 * 24 * 30, 'month', 'months'),
		array(60 * 60 * 24 * 7, 'week', 'weeks'),
		array(60 * 60 * 24, 'day', 'days'),
		array(60 * 60, 'hour', 'hours'),
		array(60, 'minute', 'minutes'),
		array(1, 'second', 'seconds'),
	);
	
	$difference = ($now - $original);

	for($i = 0, $c = count($chunks); $i < $c; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];
		if(0 != $count = floor($difference / $seconds)) {
			break;
		}
	}
	
	$since = $count." ".((1 == $count) ? $name : $names);
	
	if($detailed && $i + 1 < $c) {
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if(0 != $count2 = floor(($difference - $seconds * $count) / $seconds2)) {
			$since .= ", ".$count2." ".((1 == $count2) ? $name2 : $names2);
		}
	}
	
	return $since;
}

/**
 * Time From
 */
function timefrom($original, $detailed = 0) {
	$now = time();
	
	$chunks = array(
		array(60 * 60 * 24 * 365, 'year', 'years'),
		array(60 * 60 * 24 * 30, 'month', 'months'),
		array(60 * 60 * 24 * 7, 'week', 'weeks'),
		array(60 * 60 * 24, 'day', 'days'),
		array(60 * 60, 'hour', 'hours'),
		array(60, 'minute', 'minutes'),
		array(1, 'second', 'seconds'),
	);
	
	$difference = ($original - $now);

	for($i = 0, $c = count($chunks); $i < $c; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];
		if(0 != $count = floor($difference / $seconds)) {
			break;
		}
	}
	
	$from = $count." ".((1 == $count) ? $name : $names);
	
	if($detailed && $i + 1 < $c) {
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if(0 != $count2 = floor(($difference - $seconds * $count) / $seconds2)) {
			$from .= ", ".$count2." ".((1 == $count2) ? $name2 : $names2);
		}
	}
	
	return $from;
}
?>