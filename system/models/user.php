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

class User extends Model
{
	protected static $_name = 'users';
	protected static $_properties = array(
		'id',
		'username',
		'password',
		//'salt',
		'name',
		'email',
		'group_id',
		'login_hash'
	);
	
	protected static $_belongs_to = array('group');
	
	protected static $_filters_before = array(
		'create' => array('_before_create')
	);
	
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
	
	public function _before_create()
	{
		$this->_data['name'] = $this->_data['username'];
		$this->_data['password'] = sha1($this->_data['password']);
		$this->_data['login_hash'] = sha1(time() . $this->_data['username'] . rand(0, 1000));
	}
	
	public function is_valid()
	{
		$errors = array();
		
		// Check if the username is set
		if (empty($this->_data['username']))
		{
			$errors['username'] = l('error:user:username_blank');
		}
		
		// Check if the username is taken
		if ($this->_is_new() and static::find('username', $this->_data['username']))
		{
			$errors['username'] = l('error:user:username_in_use');
		}
		
		// Check if the password is set
		if (empty($this->_data['password']))
		{
			$errors['password'] = l('error:user:password_blank');
		}
		
		// Check if the email is set
		if (empty($this->_data['email']))
		{
			$errors['email'] = l('error:user:email_invalid');
		}
		
		// Check if we're valid or not...
		if (count($errors) > 0)
		{
			$this->errors = $errors;
		}
		
		return !count($errors) > 0;
	}
}