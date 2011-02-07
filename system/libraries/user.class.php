<?php
/**
 * Traq 2
 * Copyright (c) 2009, 2010 Jack Polgar
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
 *
 * $Id$
 */

class User
{
	// Default/Guest user info
	public $info = array(
		'id'=>'0',
		'username' => 'Guest',
		'name' => '',
		'group_id' => '3'
		);
	// Group info holder
	public $group = NULL;
	// Pretty obvious,
	// true = logged in, false = logged out
	public $loggedin = false;
	// Errors holder
	public $errors = array();
	
	/**
	 * Consturct
	 * Starts the User class.
	 */
	public function __construct()
	{
		global $db;
		
		if(!isset($_COOKIE['traq_u'])) $_COOKIE['traq_u'] = '';
		if(!isset($_COOKIE['traq_h'])) $_COOKIE['traq_h'] = '';
		
		// Check if the user cookies are set and valid.
		$query = $db->query("SELECT * FROM ".DBPF."users WHERE username='".$db->es($_COOKIE['traq_u'])."' AND sesshash='".$db->es($_COOKIE['traq_h'])."' LIMIT 1");
		if($db->numrows($query))
		{
			// Logged in...
			$this->info = $db->fetcharray($query);
			$this->loggedin = true;
		}
		
		// Get group info
		$this->group = $db->queryfirst("SELECT * FROM ".DBPF."usergroups WHERE id='".$this->info['group_id']."' LIMIT 1");
		
		($hook = FishHook::hook('user_construct')) ? eval($hook) : false;
	}
	
	/**
	 * User login function
	 * Used to login users.
	 * @access public
	 * @param string $username Username
	 * @param string $password Password
	 * @return integer
	 */
	public function login($username,$password,$remember=0) {
		global $db;
		
		$login = $db->query("SELECT * FROM ".DBPF."users WHERE username='".$db->es($username)."' AND password='".sha1($db->es($password))."' LIMIT 1");
		if($db->numrows($login)) {
			$db->query("UPDATE ".DBPF."users SET sesshash='".$db->es(sha1($password.time().$username))."' WHERE username='".$db->es($username)."' LIMIT 1");
			
			// Set the cookies
			if($remember) {
				// Remember
				setcookie('traq_u',$username,time()+9999999,'/');
				setcookie('traq_h',sha1($password.time().$username),time()+9999999,'/');
				setcookie('traq_remember',1,time()+9999999,'/');
			} else {
				// Session
				setcookie('traq_u',$username,0,'/');
				setcookie('traq_h',sha1($password.time().$username),0,'/');
				setcookie('traq_remember',0,0,'/');
			}
			($hook = FishHook::hook('user_login_success')) ? eval($hook) : false;
			return true;
		} else {
			unset($this->errors);
			$this->errors[] = l('error_invalid_username_or_password');
			($hook = FishHook::hook('user_login_error')) ? eval($hook) : false;
			return false;
		}
	}
	
	/**
	 * Logout
	 * Used to clear values of the user's cookies.
	 */
	public function logout()
	{
		setcookie('traq_u','',0,'/');
		setcookie('traq_h','',0,'/');
		setcookie('traq_remember',0,0,'/');
		($hook = FishHook::hook('user_logout')) ? eval($hook) : false;
	}
	
	/**
	 * Register
	 * Used to easily register a user account.
	 * @params array $data User data array.
	 * @return bool True or False if the account was created.
	 */
	public function register($data)
	{
		global $db;
		
		// Check for errors
		$errors = array();
		if($db->numrows($db->query("SELECT username FROM ".DBPF."users WHERE username='".$db->escapestring($data['username'])."' LIMIT 1")))
			$errors['username'] = l('error_username_taken');
		
		if(empty($data['username']))
			$errors['username'] = l('error_username_empty');
		
		if(empty($data['password']))
			$errors['password'] = l('error_password_empty');
		
		if($data['password'] != $data['password2'])
			$errors['password'] = l('error_password_nomatch');
		
		if(empty($data['email']))
			$errors['email'] = l('error_email_empty');
		
		if(count($errors) > 0)
		{
			$this->errors = $errors;
			return false;
		}
		unset($data['password2']);
		
		// If no errors, create the account.
		if(!$this->errors)
		{
			// sha1 the password
			$data['password'] = sha1($data['password']);
			
			// Little extras
			if($data['name'] == '') $data['name'] = $data['username'];
			
			// Build the query
			$fields = array();
			$values = array();
			($hook = FishHook::hook('user_register')) ? eval($hook) : false;
			foreach($data as $field => $value) {
				$fields[] = $field;
				$values[] = "'".$value."'";
			}
			$fields = implode(',',$fields);
			$values = implode(',',$values);
			
			$db->query("INSERT INTO ".DBPF."users ($fields) VALUES($values)");
			
			return true;
		}
	}
	
	/**
	 * Get User Info
	 * Used to easily get the given user ID's info.
	 * @params integer $userid The useris ID.
	 * @return array
	 */
	public function getinfo($userid)
	{
		global $db;
		return $db->queryfirst("SELECT * FROM ".DBPF."users WHERE id='".$db->res($userid)."' LIMIT 1");
	}
	
	/**
	 * Get Users
	 * Returns an array of the users in the database.
	 * @return array
	 */
	public function getusers()
	{
		global $db;
		$users = array();
		$fetch = $db->query("SELECT id,username FROM ".DBPF."users ORDER BY id ASC");
		while($info = $db->fetcharray($fetch))
			$users[] = $info;
		
		return $users;
	}
}
?>