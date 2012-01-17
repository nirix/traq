<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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
 * @return array
 * @package traq
 * @subpackage locale
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
 * @return array
 * @package traq
 * @subpackage locale
 */
function enus_locale()
{
	return array(
		'copyright' => "Powered by Traq " . TRAQ_VER . " &copy; 2009-" . date("Y"),
		'projects' => "Projects",
		'project_info' => "Project Info",
		'tickets' => "Tickets",
		'timeline' => "Timeline",
		'summary' => "Summary",
		'status' => "Status",
		'owner' => "Owner",
		'type' => "Type",
		'component' => "Component",
		'milestone' => "Milestone",
		'description' => "Description",
		'login' => "Login",
		'logout' => "Logout",
		'usercp' => "UserCP",
		'admincp' => "AdminCP",
		'register' => "Register",
		'username' => "Username",
		'password' => "Password",
		
		// Errors
		'error:404_title' => "Woops",
		'error:404_message' => "The requested page '{1}' couldn't be found.",
		'error:invalid_username_or_password' => "Invalid Username or Password.",
	);
}