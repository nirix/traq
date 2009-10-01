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
		'login' => 'Guest',
		'name' => '',
		'group_id' => '3'
		);
	public $group = NULL;
	public $loggedin = false;
	
	/**
	 * Consturct
	 * Starts the User class.
	 */
	public function __construct()
	{
		global $db;
		
		// Check if the user cookies are set and valid.
		$query = $db->query("SELECT * FROM ".DBPF."users WHERE login='".$db->es($_COOKIE['traq_u'])."' AND sesshash='".$db->es($_COOKIE['traq_h'])."' LIMIT 1");
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
		
		$login = $db->query("SELECT * FROM ".DBPF."users WHERE login='".$db->es($username)."' AND password='".sha1($db->es($password))."' LIMIT 1");
		if($db->numrows($login)) {
			$this->db->query("UPDATE ".DBPF."users SET hash='".$db->es(sha1($password.time().$username))."' WHERE username='".$db->es($username)."' LIMIT 1");
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
			$this->errors[] = l('invalid_username_or_password');
			return false;
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
}
?>