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
 * View handler
 * @package Meridian
 * @todo Make it so render() pushes to the output buffer while get() returns the view code.
 */
class View
{
	private static $ob_level;
	public static $theme;
	public static $inherit_from;
	private static $vars = array();
	
	public static function render($file, array $vars = array())
	{
		// Make the given vars accessible.
		if(count($vars))
		{
			foreach($vars as $_var => $_val)
				$$_var = $_val;
		}
		
		if(self::$ob_level === null) self::$ob_level = ob_get_level();
		
		foreach(self::$vars as $_var => $_val)
			$$_var = $_val;
		
		$file = strtolower($file);
		
		// Check if the theme has this view
		if(self::$theme != null and file_exists(APPPATH.'views/'.(self::$theme != null ? self::$theme.'/' : '').$file.'.php'))
		{
			$file = APPPATH.'views/'.self::$theme.'/'.$file.'.php';
		}
		// I guess not, let's see if we can inherit it?
		elseif(self::$inherit_from != null and file_exists((self::$inherit_from != null ? self::$inherit_from.'/' : '').$file.'.php'))
		{
			$file = self::$inherit_from.'/'.$file.'.php';
		}
		// No? Well what about the root of the views direcotry?
		elseif(file_exists(APPPATH.'views/'.$file.'.php'))
		{
			$file = APPPATH.'views/'.$file.'.php';
		}
		// Not there either? I'm not sure then..
		else
		{
			Meridian::error('View Error', 'Unable to load view: '.$file);
		}
		
		ob_start();
		include($file);
		if(ob_get_level() > self::$ob_level + 1)
		{
			//if($return)
			//{
			//	$content = ob_get_contents();
			//	ob_end_clean();
			//	return $content;
			//}
			//else
			//{
				ob_end_flush();
			//}
		}
		else
		{
			Output::append(ob_get_contents());
			@ob_end_clean();
		}
	}
	
	public static function set($var, $val)
	{
		self::$vars[$var] = $val;
	}
	
	public static function vars()
	{
		return self::$vars;
	}
}