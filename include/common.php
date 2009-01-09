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
 * Ticket Status
 * Gets the Ticket Status text.
 */
function ticketstatus($statusid) {
	$statusses = array(
					   0 => 'Closed',
					   1 => 'New',
					   2 => 'Assigned',
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
					   0 => 'Closed',
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
?>