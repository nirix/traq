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
 * Traq Routes
 * @package Traq
 * @todo Allow plugins to add their own without the last route intercepting them.
 */
Router::$routes = array(
	'/' => 'Projects::index',

	// Users
	'login' => 'User::login',
	'register' => 'User::register',

	// Admin
	'admincp/(:any)/(:any)' => "AdminCP::$1::$2",

	// Projects
	':any/roadmap' => 'Projects::roadmap',
	':any/timeline' => 'Projects::timeline',
	':any/milestones/:any' => 'Projects::milestone',
	':any/tickets' => 'Tickets::view',
	':any' => 'Projects::view', // MUST be the last route as it captches anything and everything.
);