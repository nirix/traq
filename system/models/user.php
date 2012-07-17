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
		'password_ver',
		'name',
		'email',
		'group_id',
		'login_hash'
	);
	
	// Things the user belongs to
	protected static $_belongs_to = array('group');
	
	// Things the user has many of
	protected static $_has_many = array(
		'tickets',
		
		'ticket_updates' => array('model' => 'tickethistory'),
		'assigned_tickets' => array('model' => 'ticket', 'foreign_key' => 'assigned_to_id')
	);
	
	// Things to do before certain things
	protected static $_filters_before = array(
		'create' => array('_before_create')
	);

	// Users group and role ermissions
	protected $permissions = array(
		'project' => array(),
		'role' => array()
	);
	
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
	 * Returns an array containing an array of the project and role
	 * the user belongs to.
	 *
	 * @return array
	 */
	public function projects()
	{
		$projects = array();
		
		foreach (UserRole::select()->where('user_id', $this->_data['id'])->exec()->fetch_all() as $relation)
		{
			$projects[] = array($relation->project, $relation->role);
		}

		
		return $projects;
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
		if (!isset($this->permissions['project'][$project_id]))
		{
			$this->permissions['project'][$project_id] = Permission::get_permissions($project_id, $this->_data['group_id']);
		}
		
		// Check if the user has a role for the project and
		// fetch the permissions if not already done so...
		if (!isset($this->permissions['role'][$project_id]))
		{
			$this->permissions['role'][$project_id] = Permission::get_permissions($project_id, $this->get_project_role($project_id), 'role');
		}

		// Check if user is admin...
		if ($this->group->is_admin)
		{
			return true;
		}
		
		$perms = array_merge($this->permissions['project'][$project_id], $this->permissions['role'][$project_id]);

		if (!isset($perms[$action]))
		{
			return false;
		}

		return $perms[$action]->value;		
	}
	
	/**
	 * Fetches the users project role.
	 *
	 * @param integer $project_id
	 *
	 * @return integer
	 */
	public function get_project_role($project_id)
	{
		if ($role = UserRole::select()->where('project_id', $project_id)->where('user_id', $this->_data['id'])->exec()
		and $role->row_count() > 0)
		{
			return $role->fetch()->project_role_id;
		}
		else
		{
			return 0;
		}
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
		switch($this->_data['password_ver'])
		{
			// Passwords from Traq 0.1 to 2.3
			case 'sha1':
				return sha1($password) == $this->_data['password'];
				break;

			// Passwords from Traq 3+
			case 'crypt':
				return crypt($password, $this->_data['password']) == $this->_data['password'];
				break;
		}
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
		$this->_data['password'] = crypt($this->_data['password'], '$2a$10$' . sha1(microtime() . $this->_data['username'] . $this->_data['email']) . '$');
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
	 * Returns an array formatted for the Form::select() method.
	 *
	 * @return array
	 */
	public static function select_options()
	{
		$options = array();
		foreach (static::fetch_all() as $user)
		{
			$options[] = array('label' => $user->name, 'value' => $user->id);
		}
		return $options;
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