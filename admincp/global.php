<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Set the full path to the Traq folder
define('TRAQPATH',str_replace(pathinfo('../index.php',PATHINFO_BASENAME),'','../index.php'));

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
		'title' => l('overview'),
		'url' => './',
		'active' => activepage('index.php'),
		'class' => 'first'
	),
	array(
		'title' => l('settings'),
		'url' => 'settings.php',
		'active' => activepage('settings.php')
	),
	'projects' => array(
		'title' => l('projects'),
		'url' => 'projects.php',
		'active' => activepage('projects.php'),
		'links' => array(
			array(
				'title' => l('new'),
				'url' => 'projects.php?new',
				'active' => activepage('projects.php','new')
			),
		)
	),
	'tickets' => array(
		'title' => l('tickets'),
		'url' => 'tickets.php',
		'active' => activepage('tickets.php'),
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
	'users' => array(
		'title' => l('users'),
		'url' => 'users.php',
		'active' => activepage('users.php','edit&user='.$_REQUEST['user']),
		'links' => array(
			array(
				'title' => l('new'),
				'url' => '#',
				'active' => activepage('groups.php','new')
			),
			array(
				'title' => l('manage'),
				'active' => activepage('groups.php','edit&group='.$_REQUEST['group'])
			)
		)
	),
	'plugins' => array(
		'title' => l('plugins'),
		'url' => 'plugins.php',
		'active' => activepage('plugins.php'),
		'links' => array(
			array(
				'title' => l('install'),
				'url' => 'plugins.php?install',
				'active' => activepage('plugins.php','install')
			),
			array(
				'title' => l('create_plugin'),
				'url' => 'plugins.php?create',
				'active' => activepage('plugins.php','create')
			),
			array(
				'title' => l('create_hook'),
				'url' => 'plugins.php?newhook',
				'active' => activepage('plugins.php','newhook')
			)
		)
	)
);

$hook_locations = array(
	'global',
	'formattext'
);
?>