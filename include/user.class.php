<?php
/**
 * Origin
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

/**
 * User Class
 */
class User {
	/**
	 * This holds the users info
	 * @access public
	 * @var array
	 */
	public $info = array();
	
	/**
	 * This is used to easily check if the user is logged in or not
	 * @access public
	 * @var integer
	 */
	public $loggedin = false;
	
	/**
	 * This is the default user info values pulled from the database
	 * @access private
	 * @var array
	 */
	private $userfields = array();
	
	/**
	 * Cookie settings
	 * @access public
	 * @var array
	 */
	public $cookie = array(
					'path' => '/', // Cookie path
					'domain' => '' // Cookie domain
					);
	/**
	 * Errors array
	 * @access public
	 * @var mixed
	 */
	public $errors = NULL;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		global $db;
		$this->db =& $db;
		$this->init();
	}
	
	/**
	 * This function does all the required things for the user stuff...?
	 * @access public
	 */
	public function init() {
		// Get the userfields
		$this->getfields();
		// Check the cookie values and set the appropriate variables
		$checklogin = $this->db->query("SELECT * FROM ".$this->db->prefix."users WHERE username='".$this->db->escapestring($_COOKIE['origin_user'])."' AND hash='".$this->db->escapestring($_COOKIE['origin_hash'])."' LIMIT 1");
		if($this->db->numrows($checklogin)) {
			$this->loggedin = true;
			$this->info = (object) $this->db->fetcharray($checklogin);
			$this->group = (object) $this->db->fetcharray($this->db->query("SELECT * FROM ".$this->db->prefix."usergroups WHERE id='".$this->info->groupid."' LIMIT 1"));
			if($_COOKIE['origin_remember']) {
				setcookie('origin_user',$_COOKIE['origin_user'],time()+9999999,$this->cookie['path'],$this->cookie['domain']);
				setcookie('origin_hash',$_COOKIE['origin_hash'],time()+9999999,$this->cookie['path'],$this->cookie['domain']);
				setcookie('origin_remember',1,time()+9999999,$this->cookie['path'],$this->cookie['domain']);
			}
		} else {
			$this->info = (object) $this->userfields;
			$this->info->id = 0;
			$this->info->groupid = 3;
			$this->group = (object) $this->db->fetcharray($this->db->query("SELECT * FROM ".$this->db->prefix."usergroups WHERE id='3' LIMIT 1"));
		}
	}
	
	/**
	 * User login function
	 * @access public
	 * @param string $username Username
	 * @param string $password Password
	 * @return integer
	 */
	public function login($username,$password,$remember=0) {
		$login = $this->db->query("SELECT * FROM ".$this->db->prefix."users WHERE username='".$this->db->escapestring($username)."' AND password='".sha1($this->db->escapestring($password))."' LIMIT 1");
		if($this->db->numrows($login)) {
			$this->db->query("UPDATE ".$this->db->prefix."users SET hash='".$this->db->escapestring(sha1($password.time().$username))."' WHERE username='".$this->db->escapestring($username)."' LIMIT 1");
			if($remember) {
				setcookie('origin_user',$username,time()+9999999,$this->cookie['path'],$this->cookie['domain']);
				setcookie('origin_hash',sha1($password.time().$username),time()+9999999,$this->cookie['path'],$this->cookie['domain']);
				setcookie('origin_remember',1,time()+9999999,$this->cookie['path'],$this->cookie['domain']);
			} else {
				setcookie('origin_user',$username,0,$this->cookie['path'],$this->cookie['domain']);
				setcookie('origin_hash',sha1($password.time().$username),0,$this->cookie['path'],$this->cookie['domain']);
				setcookie('origin_remember',0,0,$this->cookie['path'],$this->cookie['domain']);
			}
			return true;
		} else {
			unset($this->errors);
			$this->errors[] = "Invalid Username and/or Password.";
			return false;
		}
	}
	
	/**
	 * Register user function
	 * @access public
	 * @param array $data User Data
	 * @return integer
	 */
	public function register($data) {
		// User fields
		$bits = $this->userfields;
		$data['id'] = 0;
		$data['groupid'] = 2;
		// Check for errors
		unset($this->errors);
		if($this->db->numrows($this->db->query("SELECT username FROM ".$this->db->prefix."users WHERE username='".$this->db->escapestring($data['username'])."' LIMIT 1"))) {
			$errors['username'] = 'Username is taken.';
		}
		if(empty($data['username'])) {
			$errors['username'] = 'Username cannot be blank.';
		}
		if(empty($data['password'])) {
			$errors['password'] = 'You must enter a password.';
		}
		if(empty($data['email'])) {
			$errors['email'] = 'You must enter an email.';
		}
		if(is_array($errors)) {
			$this->errors = $errors;
			$register = 0;
			return false;
		} else {
			$register = true;
		}
		// Register the user
		if($register) {
			// SHA1 the password
			$data['password'] = sha1($data['password']);
			// Make the query values
			foreach($bits as $field => $value) {
				if(isset($data[$field])) {
					$bits[$field] = $data[$field];
				} else {
					$bits[$field] = $value;
				}
			}
			// Add the single quoutes around the values
			foreach($bits as $field => $value) {
				$values[$field] = "'".$this->db->escapestring($value)."'";
			}
			$queryvalues = implode(',',$values);
			// Run the query
			$this->db->query("INSERT INTO ".$this->db->prefix."users VALUES ($queryvalues)");
			return 1;
		} else {
			return 0;
		}
	}
	
	/**
	 * Logout function
	 * @access public
	 */
	public function logout() {
		setcookie('origin_user','',0,$this->cookie['path'],$this->cookie['domain']);
		setcookie('origin_hash','',0,$this->cookie['path'],$this->cookie['domain']);
	}
	
	// This gets the default user values from the database and stores them in an array
	private function getfields() {
		$getfields = $this->db->query("SHOW COLUMNS FROM ".$this->db->prefix."users");
		while($info = $this->db->fetcharray($getfields)) {
			$this->userfields[$info['Field']] = $info['Default'];
		}
	}
	
	/**
	 * Get a specific users info
	 * @access public
	 */
	public function getinfo($userid) {
		return $this->db->fetcharray($this->db->query("SELECT * FROM ".$this->db->prefix."users WHERE id='".$userid."' LIMIT 1"));
	}
}
?>