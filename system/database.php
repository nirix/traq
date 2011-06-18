<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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
 */

class Database
{

	public static $db;
	
	public static function init()
	{
		require APPPATH . '/config/database.php';
		
		static::$db = new Zend_Db_Adapter_Pdo_Mysql(array(
			'host'     => $db['host'],
			'username' => $db['user'],
			'password' => $db['pass'],
			'dbname'   => $db['name']
		));
		
		Zend_Db_Table_Abstract::setDefaultAdapter(static::$db);

		foreach(scandir(APPPATH . '/models') as $file) {
			if(!is_dir($file)) {
				require(APPPATH . '/models/' . $file);
			}
		}
	}
}