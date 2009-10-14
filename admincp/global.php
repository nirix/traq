<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Fetch required files
include(TRAQPATH."inc/global.php");
include("common.php");

// Get the filename of the current page
$pagebits = explode('/',$_SERVER['PHP_SELF']);
define("THISPAGE",$pagebits[sizeof($pagebits)-1]);
unset($pagebits);

// Sidebar links
$sidebar_links = array(
	array(
		'title' => l('traq'),
		'links' => array(
			array(
				'title' => l('settings'),
				'url' => 'settings.php',
				'active' => activepage('settings.php')
			),
			array(
				'title' => l('view_site'),
				'url' => '../'
			)
		)
	),
	array(
		'title' => l('projects'),
		'links' => array(
			array(
				'title' => l('new'),
				'url' => 'projects.php?new',
				'active' => activepage('projects.php','new')
			),
			array(
				'title' => l('manage'),
				'url' => 'projects.php',
				'active' => activepage('projects.php')
			)
		)
	),
	array(
		'title' => l('tickets'),
		'links' => array(
			array(
				'title' => l('manage_types'),
				'url' => 'tickets.php?types',
				'active' => activepage('tickets.php?types','tickets.php?types&type='.$_REQUEST['type'])
			),
			array(
				'title' => l('manage_priorities'),
				'url' => 'tickets.php?priorities',
				'active' => activepage('tickets.php?priorities','tickets.php?priorities&priority='.$_REQUEST['priority'])
			),
			array(
				'title' => l('manage_severities'),
				'url' => 'tickets.php?severities',
				'active' => activepage('tickets.php?severities','tickets.php?severities&severity='.$_REQUEST['severity'])
			),
			array(
				'title' => l('manage_status_types'),
				'url' => 'tickets.php?statustypes',
				'active' => activepage('tickets.php?statustypes','tickets.php?statustypes&status='.$_REQUEST['status'])
			)
		)
	),
	array(
		'title' => l('users'),
		'url' => 'users.php',
		'active' => activepage('users.php','','edit&user='.$_REQUEST['user'])
	),
	array(
		'title' => l('usergroups'),
		'links' => array(
			array(
				'title' => l('new'),
				'url' => '#',
				'active' => activepage('groups.php','','new')
			),
			array(
				'title' => l('manage'),
				'active' => activepage('groups.php','','','edit&group='.$_REQUEST['group'])
			)
		)
	)
);
?>