<?php
/**
 * FishHook 3.0 for Traq 2
 * Copyright (C) 2010 Jack Polgar
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

class FishHook
{
	private static $code = array();
	
	/**
	 * Hook
	 * Used to fetch plugin code for the specified hook.
	 */
	public static function hook($hook)
	{
		global $db;
		
		// Check if it's cached
		if(isset(self::$code[$hook])) return self::$code[$hook];
		
		// Fetch the plugin code from the DB.
		$code = array();
		$fetch = $db->query("SELECT * FROM ".DBPF."plugin_code WHERE hook='".$db->res($hook)."' AND enabled='1' ORDER BY execorder ASC");
		while($info = $db->fetcharray($fetch))
			$code[] = $info['code'];
			
		// Cache it
		self::$code[$hook] = implode(" /* */ ",$code);
		
		return self::$code[$hook];
	}
}
?>