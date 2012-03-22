<?php
/*
 * FishHook
 * Copyright (C) 2009-2012 Traq.io
 * 
 * FishHook is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * FishHook is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with FishHook. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The FishHook plugin library
 *
 * @package FishHook
 * @author Jack P.
 * @copyright (C) 2009-2012 Jack P.
 * @version 4.0
 */
class FishHook
{
	private static $_version = '4.0';
	private static $_plugins = array();
	
	/**
	 * Adds a plugin to the library
	 *
	 * @param string $class
	 * @param mixed $plugin String of the function or array of the class and method.
	 */
	public static function add($hook, $plugin)
	{
		// Make sure the hook index exists
		if (!isset(static::$_plugins[$hook]))
		{
			static::$_plugins[$hook] = array();
		}
		
		// Add the plugin 
		static::$_plugins[$hook][] = $plugin;
	}
	
	/**
	 * Executes a hook
	 *
	 * @param string $hook
	 */
	public static function run($hook, $params = array())
	{
		if (!is_array($params))
		{
			$params = array(&$params);
		}
		
		// Make sure the hook index exists
		if (!isset(static::$_plugins[$hook]))
		{
			return false;
		}
		
		// Run the hook
		foreach (static::$_plugins[$hook] as $plugin)
		{
			call_user_func_array($plugin, &$params);
		}
	}
}