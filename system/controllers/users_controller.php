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

use avalon\core\Load;

/**
 * User controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class UsersController extends AppController
{
	/**
	 * User profile page.
	 *
	 * @param integer $user_id
	 */
	public function action_view($user_id)
	{
		// If the user doesn't exist
		// display the 404 page.
		if (!$user = User::find($user_id))
		{
			return $this->show_404();
		}

		// Set the title
		$this->title(l('users'));
		$this->title(l('xs_profile', $user->name));
		
		Load::helper('tickets');
		View::set('profile', $user);
	}

	/**
	 * Handles the login page.
	 */
	public function action_login()
	{
		// Set the title
		$this->title(l('login'));
		
		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Try to find the user in the database and verify their password
			if ($user = User::find('username', Request::$post['username'])
			and $user->verify_password(Request::$post['password']))
			{
				// User found and verified, set the cookie and redirect them
				// to the index page if no "goto" page was set.
				setcookie('_traq', $user->login_hash, time() + (2 * 4 * 7 * 24 * 60 * 60 * 60), '/');
				Request::redirect(isset(Request::$post['goto']) ? Request::$post['goto'] : Request::base());
			}
			// No user found
			else
			{
				View::set('error', true);
			}
		}
	}
	
	/**
	 * Handles the logout request.
	 */
	public function action_logout()
	{
		setcookie('_traq', sha1(time()), time() + 5, '/');
		Request::redirect(Request::base());
	}
	
	/**
	 * Handles the register page and account creation.
	 */
	public function action_register()
	{
		$this->title(l('register'));
		
		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Build the data array
			$data = array(
				'username' => Request::$post['username'],
				'name' => Request::$post['name'],
				'password' => Request::$post['password'],
				'email' => Request::$post['email']
			);
			
			// Create a model with the data
			$user = new User($data);
			
			// Check if the model is valid
			if ($user->is_valid())
			{
				// Save the model and redirect to the login page.
				$user->save();
				Request::redirect(Request::base('login'));
			}
			
			View::set('user', $user);
		}
	}
}
