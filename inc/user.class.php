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
}
?>