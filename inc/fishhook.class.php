<?php
/**
 * FishHook 3.0 for Traq 2
 * The ultimate plugin library.
 *
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * http://jackpolgar.com
 */

class FishHook
{
	/**
	 * Hook
	 * Used to fetch plugin code for the specified hook.
	 */
	public function hook($hook)
	{
		global $db;
		
		// Fetch the plugin code from the DB.
		$code = array();
		$fetch = $db->query("SELECT * FROM ".DBPF."plugin_hooks WHERE hook='".$db->es($hook)."' AND enabled='1' ORDER BY loadorder ASC");
		while($info = $db->fetcharray($fetch))
		{
			$code[] = $info['code'];
		}
		
		return implode(" /* */ ",$code);
	}
}
?>