<?php
/**
 * Traq
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

/**
 * Template
 * Used to easily fetch templates.
 * @param string $template Template Name
 * @return string
 */
function template($template) {
	global $origin;
	return $origin->template->load($template);
}

/**
 * Build Title
 * Makes the page title
 * @param array $title Titles to combine
 * @return string
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
	return implode(" - ",$title);
}

/**
 * Is Project
 * Check if the supplied string is a project.
 * @param string $string String to check if a project exists with that slug.
 * @return integer
 */
function is_project($string) {
	global $db;
	return $db->numrows($db->query("SELECT slug FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($string)."' LIMIT 1"));
}

/**
 * Get Project Milestones
 * Used to get the milestones of the specified project.
 * @param integer $projectid Project ID
 * @return array
 */
function projectmilestones($projectid) {
	global $db;
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project=".$projectid." AND completed='0' ORDER BY milestone ASC");
	while($info = $db->fetcharray($fetchmilestones)) {
		$milestones[] = $info;
	}
	return $milestones;
}

/**
 * Get Project Components
 * Used to get the components of the specified project.
 * @param integer $projectid Project ID
 * @return array
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
 * Used to get the versions of the specified project.
 * @param integer $projectid Project ID
 * @return array
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
 * Used to get the managers of the specified project.
 * @param integer $projectid Project ID
 * @return array
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
 * @return string
 */
function ticketstatus($statusid) {
	global $db;
	$status = array();
	$fetchstatustypes = $db->query("SELECT * FROM ".DBPREFIX."statustypes ORDER BY id ASC");
	while($info = $db->fetcharray($fetchstatustypes)) {
		$status[$info['id']] = $info['name'];
	}
	return $status[$statusid];
}

/**
 * Get Status Types
 * Used to get an array of the Ticket Status types.
 * @param string $sort Field to sort by
 * @param string $order Direction to sort by, ASC or DESC
 * @return array
 */
function getstatustypes($sort='id',$order='ASC') {
	global $db;
	$status = array();
	$fetchstatus = $db->query("SELECT * FROM ".DBPREFIX."statustypes ORDER BY ".$db->escapestring($sort)." ".$db->escapestring($order)."");
	while($info = $db->fetcharray($fetchstatus)) {
		$status[$info['id']] = $info;
	}
	return $status;
}

/**
 * Ticket Priority
 * Gets the Ticket Priorty text.
 * @return string
 */
function ticketpriority($priorityid) {
	global $db;
	$priorities = array();
	$fetchpriorities = $db->query("SELECT * FROM ".DBPREFIX."priorities ORDER BY id ASC");
	while($info = $db->fetcharray($fetchpriorities)) {
		$priorities[$info['id']] = $info['name'];
	}
	return $priorities[$priorityid];
}

/**
 * Get Priorities
 * Used to get an array of the Ticket Priorities.
 * @return array
 */
function getpriorities() {
	global $db;
	$priorities = array();
	$fetchtypes = $db->query("SELECT * FROM ".DBPREFIX."priorities ORDER BY id ASC");
	while($info = $db->fetcharray($fetchtypes)) {
		$priorities[$info['id']] = $info;
	}
	return $priorities;
}

/**
 * Ticket Type
 * Gets the Ticket Type text.
 * @return string
 */
function tickettype($typeid) {
	global $db;
	$types = array();
	$fetchtypes = $db->query("SELECT * FROM ".DBPREFIX."types ORDER BY id ASC");
	while($info = $db->fetcharray($fetchtypes)) {
		$types[$info['id']] = $info['name'];
	}
	return $types[$typeid];
}

/**
 * Get Types
 * Used to get an array of the Ticket Types.
 * @return array
 */
function gettypes() {
	global $db;
	$types = array();
	$fetchtypes = $db->query("SELECT * FROM ".DBPREFIX."types ORDER BY id ASC");
	while($info = $db->fetcharray($fetchtypes)) {
		$types[$info['id']] = $info;
	}
	return $types;
}

/**
 * Ticket Severity
 * Gets the Ticket Severity text.
 * @return string
 */
function ticketseverity($severityid) {
	global $db;
	$severities = array();
	$fetchseverities = $db->query("SELECT * FROM ".DBPREFIX."severities ORDER BY id ASC");
	while($info = $db->fetcharray($fetchseverities)) {
		$severities[$info['id']] = $info['name'];
	}
	return $severities[$severityid];
}

/**
 * Get Severities
 * Used to get an array of the ticket severities.
 * @return array
 */
function getseverities() {
	global $db;
	$severities = array();
	$fetchseverities = $db->query("SELECT * FROM ".DBPREFIX."severities ORDER BY id ASC");
	while($info = $db->fetcharray($fetchseverities)) {
		$severities[$info['id']] = $info;
	}
	return $severities;
}

/**
 * Text Formatting
 * Used to format tickets, comments, descriptions, etc.
 * @param string $text Input text
 * @return string
 */
function formattext($text) {
	global $origin;
	$text_orig = $text;
	$text = htmlspecialchars($text);
	// Strip Slashes
	$text = stripslashes($text);
	// [comment:X User] format
	$text = commentTag($text);
	// Plugin Hook Filter
	$text = FishHook::filter('common_formattext_text',$text);
	// BBCode
	$text = $origin->bbcode->format($text,false);
	// Return  for display
	return str_replace("\n\r","<br /><br />",$text);
}

/**
 * Comment Tag
 * Used to format the [comment:X User] tag to the proper code.
 * @param string $text Input Text
 * @return string
 */
function commentTag($text) {
	$bits = preg_split('/\[(\b(comment:)'.'[^][<>"\\x00-\\x20\\x7F]+) *([^\]\\x0a\\x0d]*?)\]/S', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
	$i = 0;
	// Loop through the bits and replace
	while($i<count($bits)) {
		$i++;
		if($bits[$i] != "") {
			$id = $bits[$i++]; // Comment ID
			$i++;
			$user = $bits[$i++]; // User
			$text = str_replace("[".$id." ".$user."]","<a href=\"#".$id."\">".$user."</a>",$text);
		}
	}
	return $text;
}

/**
 * Calcuate Percent
 * Used to calculate the percent of two numbers,
 * if both numbers are the same, 100(%) is returned.
 * @param integer $min Lowest number
 * @param integer $max Highest number
 * @return integer
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
 * @param integer $original Original Timestamp
 * @param integer $detailed Detailed format or not
 * @return string
 */
function timesince($original, $detailed = false) {
	$now = time(); // Get the time right now...
	
	// Time chunks...
	$chunks = array(
		array(60 * 60 * 24 * 365, 'year', 'years'),
		array(60 * 60 * 24 * 30, 'month', 'months'),
		array(60 * 60 * 24 * 7, 'week', 'weeks'),
		array(60 * 60 * 24, 'day', 'days'),
		array(60 * 60, 'hour', 'hours'),
		array(60, 'minute', 'minutes'),
		array(1, 'second', 'seconds'),
	);
	
	// Get the difference
	$difference = ($now - $original);
	
	// Loop around, get the time since
	for($i = 0, $c = count($chunks); $i < $c; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];
		if(0 != $count = floor($difference / $seconds)) {
			break;
		}
	}
	
	// Format the time since
	$since = $count." ".((1 == $count) ? $name : $names);
	
	// Get the detailed time since if the detaile variable is true
	if($detailed && $i + 1 < $c) {
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if(0 != $count2 = floor(($difference - $seconds * $count) / $seconds2)) {
			$since .= ", ".$count2." ".((1 == $count2) ? $name2 : $names2);
		}
	}
	
	// Return the time since
	return $since;
}

/**
 * Time From
 * @param integer $original Original Timestamp
 * @param integer $detailed Detailed format or not
 * @return string
 */
function timefrom($original, $detailed = false) {
	$now = time(); // Get the time right now...
	
	// Time chunks...
	$chunks = array(
		array(60 * 60 * 24 * 365, 'year', 'years'),
		array(60 * 60 * 24 * 30, 'month', 'months'),
		array(60 * 60 * 24 * 7, 'week', 'weeks'),
		array(60 * 60 * 24, 'day', 'days'),
		array(60 * 60, 'hour', 'hours'),
		array(60, 'minute', 'minutes'),
		array(1, 'second', 'seconds'),
	);
	
	// Get the difference
	$difference = ($original - $now);
	
	// Loop around, get the time from
	for($i = 0, $c = count($chunks); $i < $c; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];
		if(0 != $count = floor($difference / $seconds)) {
			break;
		}
	}
	
	// Format the time from
	$from = $count." ".((1 == $count) ? $name : $names);
	
	// Get the detailed time from if the detaile variable is true
	if($detailed && $i + 1 < $c) {
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if(0 != $count2 = floor(($difference - $seconds * $count) / $seconds2)) {
			$from .= ", ".$count2." ".((1 == $count2) ? $name2 : $names2);
		}
	}
	
	// Return the time from
	return $from;
}
?>