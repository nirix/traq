<?php
/**
 * Meridian
 * Copyright (C) 2010-2011 Jack Polgar
 * 
 * This file is part of Meridian.
 * 
 * Meridian is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Meridian is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Meridian. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Database loader
 * @package Meridian
 */
class Database
{
	public static $config;
	
	public static function init()
	{
		if(!is_array(self::$config)) return false;
		
		$driver = self::$config['driver'];
		require SYSPATH.'database/'.strtolower($driver).'/'.strtolower($driver).'.php';
		
		define("DBPREFIX", self::$config['prefix']);
		$name = 'DB_'.$driver;
		return new $name(self::$config);
	}
}