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
 * User model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class User extends Model
{
	protected static $_name = 'users';
	protected static $_properties = array(
		'id',
		'username',
		'password',
		'name',
		'email',
		'group_id',
		'login_hash'
	);
	
	protected static $_belongs_to = array('group');
	
	protected static $_has_many = array('tickets', 'ticket_updates' => array('model' => 'tickethistory'));
	
	protected static $_filters_before = array(
		'create' => array('_before_create')
	);

	protected $permissions = array();
	
	/**
	 * Returns the URI for the users profile.
	 *
	 * @return string
	 */
	public function href()
	{
		return "/users/{$this->id}";
	}

	/**
	 * Shortcut for selecting the users assigned tickets.
	 *
	 * @return object
	 */
	public function assigned_tickets()
	{
		return Ticket::select()->where('assigned_to_id', $this->_data['id']);
	}

	/**
	 * Check if the user can perform the requested action.
	 *
	 * @param integer $proejct_id
	 * @param string $action
	 *
	 * @return bool
	 */
	public function permission($project_id, $action)
	{
		// Check if the projects permissions has been fetched
		// if not, fetch them.
		if (!isset($this->permissions[$project_id]))
		{
			$this->permissions[$project_id] = Permission::get_permissions($this->_data['group_id'], $project_id);
		}

		// Return the permission for the specified action
		return $this->permissions[$project_id][$action];
	}

	/**
	 * Checks the given password against the users password.
	 *
	 * @param string $password
	 *
	 * @return bool
	 */ 
	public function verify_password($password)
	{
		return sha1($password) == $this->_data['password'];
	}
	
	/**
	 * Handles all the required stuff before creating
	 * the user, such as hashing the password.
	 */
	protected function _before_create()
	{
		$this->prepare_password();
		$this->_data['login_hash'] = sha1(time() . $this->_data['username'] . rand(0, 1000));
		
		if (!isset($this->_data['name']))
		{
			$this->_data['name'] = $this->_data['username'];
		}
	}
	
	/**
	 * Hashes the users password.
	 */
	public function prepare_password()
	{
		$this->_data['password'] = sha1($this->_data['password']);
	}
	
	/**
	 * Checks if the users data is valid or not.
	 *
	 * @return bool
	 */
	public function is_valid()
	{
		$errors = array();
		
		// Check if the username is set
		if (empty($this->_data['username']))
		{
			$errors['username'] = l('errors.users.username_blank');
		}
		
		// Check if the username is taken
		if ($this->_is_new() and static::find('username', $this->_data['username']))
		{
			$errors['username'] = l('errors.users.username_in_use');
		}
		
		// Check if the password is set
		if (empty($this->_data['password']))
		{
			$errors['password'] = l('errors.users.password_blank');
		}
		
		// Check if the email is set
		if (empty($this->_data['email']))
		{
			$errors['email'] = l('errors.users.email_invalid');
		}
		
		// Check if we're valid or not...
		if (count($errors) > 0)
		{
			$this->errors = $errors;
		}
		
		return !count($errors) > 0;
	}

	/**
	 * Returns an array of the users data.
	 *
	 * @param array $fields Fields to return
	 *
	 * @return array
	 */
	public function __toArray($fields = null)
	{
		$data = parent::__toArray($fields);
		unset($data['password'], $data['email'], $data['login_hash']);
		return $data;
	}
}