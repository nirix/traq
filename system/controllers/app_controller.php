<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
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

require APPPATH . '/common.php';
require APPPATH . '/version.php';

/**
 * App controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AppController extends Controller
{
	public $project;
	public $user;
	public $title = array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->title(settings('title'));
		
		// Load helpers
		Load::helper('html', 'form', 'js');
		
		// Get the user info
		$this->_get_user();
		
		// Set the theme, title and pass the app object to the view.
		View::$theme = 'default';
		View::set('traq', $this);
		
		// Check if we're on a project page and get the project info
		//if (is_project(Request::seg(0)) and $this->project = Project::find('slug', Request::seg(0)) and $this->project->permission($this->user->group_id, 'view'))
		if ($this->project = is_project(Request::seg(0)) and $this->project->permission($this->user->group_id, 'view'))
		{
			$this->title($this->project->name);
			View::set('project', $this->project);
		}
	}
	
	/**
	 * Adds to or returns the page title array.
	 *
	 * @param mixed $add
	 *
	 * @return mixed
	 */
	public function title($add = null)
	{
		if ($add === null)
		{
			return $this->title;
		}
		
		$this->title[] = $add;
	}
	
	/**
	 * Used to display the 404 page.
	 * 
	 * @author Jack P.
	 * @since 3.0
	 */
	public function show_404()
	{
		// Send the request to the view and
		// change the view file to error/404.php
		// and disable the calling of the routed
		// controller method.
		View::set('request', Request::url());
		$this->_render['view'] = 'error/404';
		$this->_render['action'] = false;
	}
	
	/**
	 * Does the checking for the session cookie and fetches the users info.
	 * 
	 * @author Jack P.
	 * @since 3.0
	 * @access private
	 */
	private function _get_user()
	{
		// Check if the session cookie is set, if so, check if it matches a user
		// and set set the user info.
		if (isset($_COOKIE['_traq']) and $user = User::find('login_hash', $_COOKIE['_traq']))
		{
			$this->user = $user;
			define("LOGGEDIN", true);
		}
		// Otherwise just set the user info to guest.
		else
		{
			$this->user = new User(array(
				'id' => -1,
				'username' => l('guest'),
				'group_id' => 3
			));
			define("LOGGEDIN", false);
		}
		
		// Set the current_user variable in the views.
		View::set('current_user', $this->user);
	}
}