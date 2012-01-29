<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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
 */

/**
 * enUS localization information.
 *
 * @package Traq
 * @subpackage Locale
 *
 * @return array
 */
function enus_info()
{
	return array(
		'name' => 'English',
		'author' => 'Jack Polgar',
		'version' => '3.0'
	);
}

/**
 * enUS localization strings.
 *
 * @package Traq
 * @subpackage Locale
 *
 * @return array
 */
function enus_locale()
{
	return array(
		'copyright' => "Powered by Traq " . TRAQ_VER . " &copy; 2009-" . date("Y") . " Traq.io",
		'projects' => "Projects",
		'project_info' => "Project Info",
		'tickets' => "Tickets",
		'roadmap' => "Roadmap",
		'timeline' => "Timeline",
		'settings' => "Settings",
		'managers' => "Managers",
		'information' => "Information",
		'milestones' => "Milestones",
		'components' => "Components",
		'project_settings' => "Project Settings",
		'name' => "Name",
		'slug' => "Slug",
		'codename' => "Codename",
		'open' => "Open",
		'closed' => "Closed",
		'cancel' => "Cancel",
		'new' => "New",
		'wiki' => "Wiki",
		
		// AdminCP
		'users' => "Users",
		'groups' => "Groups",
		'new_project' => "New Project",
		'plugins' => "Plugins",
		'enabled_plugins' => "Enabled Plugins",
		'disabled_plugins' => "Disabled Plugins",
		'author' => "Author",
		'version' => "Version",
		'enable' => "Enable",
		'disable' => "Disable",
		'new_user' => "New User",
		'edit_user' => "Edit User",
		'group' => "Group",
		'new_group' => "New Group",
		'edit_group' => "Edit Group",
		'ticket_types' => "Ticket Types",
		'ticket_statuses' => "Ticket Statuses",
		'new_ticket_type' => "New Ticket Type",
		'edit_ticket_type' => "Edit Ticket Type",
		'bullet' => "Bullet",
		'show_on_changelog' => "Show on Changelog",
		'template' => "Template",
		'new_ticket_status' => "New Ticket Status",
		'edit_ticket_status' => "Edit Ticket Status",

		// Project settings
		'new_milestone' => "New Milestone",
		'edit_milestone' => "Edit Milestone",
		'new_component' => "New Component",
		'edit_component' => "Edit Component",
		
		// Tickets
		'summary' => "Summary",
		'status' => "Status",
		'owner' => "Owner",
		'type' => "Type",
		'component' => "Component",
		'milestone' => "Milestone",
		'description' => "Description",
		
		// User stuff
		'login' => "Login",
		'logout' => "Logout",
		'usercp' => "UserCP",
		'admincp' => "AdminCP",
		'register' => "Register",
		'username' => "Username",
		'password' => "Password",
		'email' => "Email",
		
		// Other
		'actions' => "Actions",
		'create' => "Create",
		'save' => "Save",
		'edit' => "Edit",
		'delete' => "Delete",
		
		// Help
		'help:slug' => "A lower case alpha-numerical string with the exception of dashes, underscores and periods to be used in the URL.",
		'help:ticket_type_bullet' => "The bullet style used on the changelog list.",
		
		// Confirmations
		'confirm:delete_x' => "Are you sure you want to delete {1}?",
		
		// Errors
		'error:404_title' => "Woops",
		'error:404_message' => "The requested page '{1}' couldn't be found.",
		'error:invalid_username_or_password' => "Invalid Username or Password.",
		'error:name_blank' => "Name cannot be blank",
		'error:slug_blank' => "Slug cannot be blank",
		'error:slug_in_use' => "That slug is already in use",
		'error:ticket_type:bullet_blank' => "Bullet cannot be blank",
		
		// User errors
		'error:user:username_blank' => "Username cannot be blank",
		'error:user:username_in_use' => "That username is already registered",
		'error:user:password_blank' => "Password cannot be blank",
		'error:user:email_invalid' => "Invalid email address",
	);
}