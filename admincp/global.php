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

// AdminCP links array.
// Stores all the links for the nav
// and sidebar.
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
	// Project pages links.
	'projects' => array(
		'title' => l('projects'),
		'url' => 'projects.php',
		'active' => activepage(array('projects.php','milestones.php','components.php','versions.php')),
		'links' => array(
			array(
				'title' => l('new_project'),
				'url' => 'projects.php?new',
				'active' => activepage('projects.php','new'),
			),
			array('divider'=>true),
			array(
				'title' => l('new_milestone'),
				'url' => 'milestones.php?new',
				'active' => activepage('milestones.php','new')
			),
			array(
				'title' => l('manage_milestones'),
				'url' => 'milestones.php',
				'active' => activepage('milestones.php','','edit')
			),
			array('divider'=>true),
			array(
				'title' => l('new_component'),
				'url' => 'components.php?new',
				'active' => activepage('components.php','new')
			),
			array(
				'title' => l('manage_components'),
				'url' => 'components.php',
				'active' => activepage('components.php','','edit')
			),
			array('divider'=>true),
			array(
				'title' => l('new_version'),
				'url' => 'versions.php?new',
				'active' => activepage('versions.php','new')
			),
			array(
				'title' => l('manage_versions'),
				'url' => 'versions.php',
				'active' => activepage('versions.php','','edit')
			)
		)
	),
	// Ticket pages links.
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
	// User pages links.
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
	// Plugin pages links.
	'plugins' => array(
		'title' => l('plugins'),
		'url' => 'plugins.php',
		'active' => activepage('plugins.php'),
		'links' => array(
			array(
				'title' => l('install_plugin'),
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

// Plugin Hook Locations.
// Holds all the hook locations in Traq.
$hook_locations = array(
	'global',
	'formattext',
	'getsettings',
	'gettemplate',
	'ticket_create',
	'ticket_update',
	'template_new_ticket_properties',
	'template_view_ticket_properties',
	'template_update_ticket_properties',
	'admin_settings'
);
?>