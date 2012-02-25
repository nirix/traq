<?php
/*
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
 * API controller.
 *
 * @author arturo182
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class APIController extends Controller
{
	public $_before;
	public $user;
	
	/**
	 * Oh hai
	 */
	public function __construct()
	{
		parent::__construct();

		Load::helper('api');
		
		// check api key for every method, lazy way
		$this->_before = array(Router::$method => array('_check_api_key'));
		
		$this->_render['layout'] = 'api';
		$this->_render['view'] = '';
	}

	/**
	 * Displays the user whose api_key is being used
	 */
	public function action_user()
	{
		$out = API::user_array($this->user);
		View::set('output', $out);
	}

	/**
	 * Displays the user with id = $id
	 */
	public function action_users($id = -1)
	{
		if (Request::$method == 'post') {
			$data = array(
				'username' => Request::$post['username'],
				'password' => Request::$post['password'],
				'email' => Request::$post['email'],
				'name' => Request::$post['name']
			);

			$user = new User($data);
			if ($user->is_valid()) {
				$user->save();
				
				$out = API::user_array($user);
				View::set('output', $out);
			} else {
				$this->show_422();
			}
		} else {
			if ($user = User::find('id', $id)) {
				$out = API::user_array($user);
				View::set('output', $out);
			} else {
				$this->show_404();
			}
		}
	}
	
	/**
	 * Displays a "Not found" error, doesn't matter what was requested
	 */
	public function show_404()
	{
		header('HTTP/1.1 404 Not found');
	
		$error = array(
			'error' => '404',
			'message' => 'Not found'
		);
		
		View::set('output', $error);
	}
	
	/**
	 * Displays a "Not found" error, doesn't matter what was requested
	 */
	public function show_422()
	{
		header('HTTP/1.1 422 Unprocessable Entity');
	
		$error = array(
			'error' => '422',
			'message' => 'Validation Failed'
		);
		
		View::set('output', $error);
	}
	
	/**
	 * Checks if api key exists
	 */
	public function _check_api_key($action)
	{
		// First tray to get api_key from HTTP header
		$api_key = $_SERVER['HTTP_TRAQ_API_KEY'];
		if(!$api_key) {
			// if header fails, lets get from parameter
			$api_key = Request::$request['api_key'];
		}
		
		// check if the key even exists
		if ($api_key and $user = User::find('api_key', $api_key)) {
			$this->user = $user;
		} else {
			header('HTTP/1.1 403 Forbidden');
		
			$this->_render['action'] = false;
		
			$error = array(
				'error' => '403',
				'message' => 'Forbidden'
			);

			View::set('output', $error);
			return false;
		}
	}
	
	/**
	 * Bye, bye
	 */
	public function __shutdown()
	{
		header('Content-Type: application/json');
		View::render("layouts/{$this->_render['layout']}");
		echo Output::body();
	}
}