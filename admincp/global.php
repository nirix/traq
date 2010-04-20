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
// Stores all the links for the nav and sidebar.
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
		'active' => activepage(array('projects.php','milestones.php','components.php','versions.php','repositories.php')),
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
			),
			array('divider'=>true),
			array(
				'title' => l('new_repository'),
				'url' => 'repositories.php?new',
				'active' => activepage('repositories.php','new')
			),
			array(
				'title' => l('manage_repositories'),
				'url' => 'repositories.php',
				'active' => activepage('repositories.php','','edit='.$_REQUEST['edit'])
			)
		)
	),
	// Ticket pages links.
	'tickets' => array(
		'title' => l('tickets'),
		'url' => 'tickets.php',
		'active' => activepage('tickets.php')
	),
	// User pages links.
	'users' => array(
		'title' => l('users'),
		'url' => 'users.php',
		'active' => activepage(array('users.php','groups.php')),
		'links' => array(
			array(
				'title' => l('new_user'),
				'url' => 'users.php?new',
				'active' => activepage('users.php','new')
			),
			array('divider'=>true),
			array(
				'title' => l('new_usergroup'),
				'url' => 'groups.php?new',
				'active' => activepage('groups.php','new')
			),
			array(
				'title' => l('manage_groups'),
				'url' => 'groups.php',
				'active' => activepage('groups.php','','edit='.$_REQUEST['edit'])
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
	
	// AdminCP
	'admincp' => array(
		'admin_settings',
		'admin_settings_save',
		'admin_global',
		'admin_repositories'
	),
	
	// Projects
	'projects' => array(
		'roadmap_fetch',
		'projectlist_fetch',
		'roadmap_fetch'
	),
	
	// Tickets
	'tickets' => array(
		'ticket_create',
		'ticket_update',
		'ticket_get',
		'ticket_delete',
		'tickets_columns'
	),
	
	// Users
	'users' => array(
		'user_construct',
		'user_register',
		'user_logout',
		'user_login_success',
		'user_login_error'
	),
	
	// Templates
	'templates' => array(
		'template_new_ticket_properties',
		'template_view_ticket_properties',
		'template_update_ticket_properties',
		'template_headerinc',
		'template_header_project_links',
		'template_footer',
		'template_projectlist_quick_nav'
	),
	
	// Functions
	'functions' => array(
		'function_formattext',
		'function_settings',
		'function_locale',
		'function_ticket_statuses',
		'function_ticket_types',
		'function_ticket_priorities',
		'function_ticket_severities',
		'function_ticket_columns',
		'function_ticket_columns',
		'function_ticket_filters',
		'function_project_milestones',
		'function_project_verions',
		'function_project_components',
		'function_project_managers',
		'function_is_subscribed',
		'function_add_subscription',
		'function_remove_subscription',
		'function_send_notification'
	),
	
	// Handlers
	'handlers' => array(
		'handler_milestone',
		'handler_newticket',
		'handler_project',
		'handler_projectlist',
		'handler_roadmap',
		'handler_ticket',
		'handler_tickets',
		'handler_timeline',
		'handler_changelog',
		'handler_source',
		'handler_usercp',
		'handler_usercp_save',
		'attachment_view'
	)
);

($hook = FishHook::hook('admin_global')) ? eval($hook) : false;
?>