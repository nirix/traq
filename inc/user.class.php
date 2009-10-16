<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

class User
{
	public $info = array(
		'id'=>'0',
		'username' => 'Guest',
		'name' => '',
		'group_id' => '3'
		);
	public $group = NULL;
	public $loggedin = false;
	public $errors = NULL;
	
	/**
	 * Consturct
	 * Starts the User class.
	 */
	public function __construct()
	{
		global $db;
		
		// Check if the user cookies are set and valid.
		$query = $db->query("SELECT * FROM ".DBPF."users WHERE username='".$db->es($_COOKIE['traq_u'])."' AND sesshash='".$db->es($_COOKIE['traq_h'])."' LIMIT 1");
		if($db->numrows($query))
		{
			// Logged in...
			$this->info = $db->fetcharray($query);
			$this->loggedin = true;
		}
		else
		{
			// Logged out...
		}
		
		// Get group info
		$this->group = $db->queryfirst("SELECT * FROM ".DBPF."usergroups WHERE id='".$this->info['group_id']."' LIMIT 1");
		
		($hook = FishHook::hook('user_construct')) ? eval($hook) : false;
	}
	
	/**
	 * User login function
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
			if($remember) {
				setcookie('traq_u',$username,time()+9999999,'/');
				setcookie('traq_h',sha1($password.time().$username),time()+9999999,'/');
				setcookie('traq_remember',1,time()+9999999,'/');
			} else {
				setcookie('traq_u',$username,0,'/');
				setcookie('traq_h',sha1($password.time().$username),0,'/');
				setcookie('traq_remember',0,0,'/');
			}
			return true;
		} else {
			unset($this->errors);
			$this->errors[] = l('error_invalid_username_or_password');
			return false;
		}
	}
	
	/**
	 * Logout
	 */
	public function logout()
	{
		setcookie('traq_u','',0,'/');
		setcookie('traq_h','',0,'/');
		setcookie('traq_remember',0,0,'/');
	}
	
	/**
	 * Register
	 */
	public function register($data)
	{
		global $db;
		
		$data['id'] = 0;
		$data['group_id'] = 2;
		
		// Check for errors
		$errors = array();
		if($db->numrows($db->query("SELECT username FROM ".DBPF."users WHERE username='".$db->escapestring($data['username'])."' LIMIT 1"))) {
			$errors['username'] = l('error_username_taken');
		}
		if(empty($data['username'])) {
			$errors['username'] = l('error_username_empty');
		}
		if(empty($data['password'])) {
			$errors['password'] = l('error_password_empty');
		}
		if($data['password'] != $data['password2']) {
			$errors['password'] = l('error_password_nomatch');
		}
		if(empty($data['email'])) {
			$errors['email'] = l('error_email_empty');
		}
		if(count($errors) > 0)
		{
			$this->errors = $errors;
			return false;
		}
		
		// If no errors, create the account.
		if(!$this->errors)
		{
			$data['password'] = sha1($data['password']);
			$userfields = $this->getfields();
			foreach($userfields as $field => $value) {
				if(isset($data[$field])) {
					$userfields[$field] = "'".$db->res($data[$field])."'";
				} else {
					$userfields[$field] = "'".$db->res($value)."'";
				}
			}
			$queryvalues = implode(',',$userfields);
			
			$db->query("INSERT INTO ".DBPF."users VALUES ($queryvalues)");
			
			return true;
		}
	}
	
	/**
	 * Get User Info
	 */
	public function getinfo($userid)
	{
		global $db;
		return $db->queryfirst("SELECT * FROM ".DBPF."users WHERE id='".$db->res($userid)."' LIMIT 1");
	}
	
	// This gets the default user values from the database and returns them in an array
	private function getfields() {
		global $db;
		$userfields = array();
		$getfields = $db->query("SHOW COLUMNS FROM ".DBPF."users");
		while($info = $db->fetcharray($getfields)) {
			$userfields[$info['Field']] = $info['Default'];
		}
		return $userfields;
	}
	
	/**
	 * Get Users
	 * Returns an array of the users in the database.
	 */
	public function getusers()
	{
		global $db;
		$users = array();
		$fetch = $db->query("SELECT id,username FROM ".DBPF."users ORDER BY id ASC");
		while($info = $db->fetcharray($fetch))
		{
			$users[] = $info;
		}
		return $users;
	}
}
?>