<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

class Database
{
	private static $link;
	private static $initiated = false;
	
	public static function init()
	{
		require APPPATH . '/config/database.php';
		require SYSPATH . '/core/model.php';
		require SYSPATH . '/database/' . $db['driver'] . '.php';
		
		$class = 'Avalon_' . $db['driver'];
		static::$link = new $class($db);
		Model::$db =& static::$link;
		
		foreach(scandir(APPPATH . '/models') as $file) {
			if(!is_dir($file)) {
				require(APPPATH . '/models/' . $file);
			}
		}
		
		static::$initiated = true;
	}
	
	public static function link()
	{
		return static::$link;
	}
	
	public static function initiated()
	{
		return static::$initiated;
	}
}