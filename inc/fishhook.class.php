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
	
	public static function import_plugin($file)
	{
		global $db;
		
		$plugin = simplexml_load_file($file);
		
		// Insert plugin
		$db->query("INSERT INTO ".DBPF."plugins VALUES(
			0,
			'".$db->res((string)$plugin->info->name)."',
			'".$db->res((string)$plugin->info->author)."',
			'".$db->res((string)$plugin->info->website)."',
			'".$db->res((string)$plugin->info->version)."',
			'1',
			'".$db->res((string)$plugin->sql->install)."',
			'".$db->res((string)$plugin->sql->uninstall)."'
		)");
		$pluginid = $db->insertid();
		
		// Run the install SQL
		if($plugin->sql->install != '')
		{
			$queries = explode(';',$plugin->sql->install);
			foreach($queries as $query)
				if($query != '')
					$db->query(str_replace('traq_',DBPF,$query));
		}
		
		// Add the hooks
		foreach($plugin->hooks->hook as $hook)
		{
			$db->query("INSERT INTO ".DBPF."plugin_code VALUES(
				0,
				'".$pluginid."',
				'".$db->res((string)$hook['title'])."',
				'".$db->res((string)$hook['hook'])."',
				'".$db->res((string)$hook->code)."',
				'".$db->res((integer)$hook['execorder'])."',
				'1'
			)");
		}
	}
}
?>