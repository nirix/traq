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
 * UserCP controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class UsercpController extends AppController
{

	public function action_password()
	{
		// Make sure the user is logged in
		if (!LOGGEDIN)
		{
			$this->show_no_permission();
		}

		// Clone the logged in user object
		$user = clone $this->user;

		if (Request::$method == 'post')
		{
			$data = array(
					'old_password' => Request::$post['password'],
					'new_password' => Request::$post['new_password'],
					'confirm_password' => Request::$post['confirm_password']
			);

			FishHook::add('controller:users::usercp/password/save', array(&$data));

			// Set the info
			$user->set($data);

			if($user->is_valid())
			{
				$user->set_password($data['new_password']);

				// Save the user
				if ($user->save())
				{
					// Redirect if successfull
					Request::redirect(Request::full_uri());
				}
			}
		}

		View::set('user', $user);
	}

	/**
	 * The index page.
	 */
	public function action_index()
	{
		// Make sure the user is logged in
		if (!LOGGEDIN)
		{
			$this->show_no_permission();
		}

		// Clone the logged in user object
		$user = clone $this->user;

		// Has the form been submitted?
		if (Request::$method == 'post')
		{
			$data = array(
				'name' => Request::$post['name'],
				'email' => Request::$post['email']
			);

			FishHook::add('controller:users::usercp/save', array(&$data));

			// Set the info
			$user->set($data);

			// Save the user
			if ($user->save())
			{
				// Redirect if successfull
				Request::redirect(Request::full_uri());
			}
		}

		View::set('user', $user);
	}
}