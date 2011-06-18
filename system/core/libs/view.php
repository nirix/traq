<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * Avalon's View class
 * @package Avalon
 */
class View
{
	private static $ob_level;
	public static $theme;
	public static $inherit_from;
	private static $vars = array();
	
	public static function render($file, array $vars = array())
	{
		// Get the view content
		$content = self::_get_view($file, $vars);
		
		// Check if we need to flush or append
		if(ob_get_level() > self::$ob_level + 1) {
			ob_end_flush();
		}
		// Append it to the output
		else {
			Output::append($content);
			@ob_end_clean();
		}
	}
	
	public static function get($file, array $vars = array())
	{
		// Get the content and clean the buffer
		$content = self::_get_view($file, $vars, true);
		ob_end_clean();
		return $content;
	}
	
	private static function _get_view($_file, array $vars = array())
	{
		// Get the file name/path
		$_file = self::_view_file_path($_file);
		
		// Make sure the ob_level is set
		if (self::$ob_level === null) {
			self::$ob_level = ob_get_level();
		}
		
		// Make the set variables accessible
		foreach (self::$vars as $_var => $_val) {
			$$_var = $_val;
		}
		
		// Make the vars for this view accessible
		if (count($vars)) {
			foreach($vars as $_var => $_val)
				$$_var = $_val;
		}
		
		// Load up the view and get the contents
		ob_start();
		include($_file);
		$content = ob_get_contents();
		
		return $content;
	}
	
	private static function _view_file_path($file)
	{
		$file = strtolower($file);
		
		// Check if the theme has this view
		if (self::$theme != null and file_exists(APPPATH.'/views/'.(self::$theme != null ? self::$theme.'/' : '').$file.'.php')) {
			$file = APPPATH.'/views/'.self::$theme.'/'.$file.'.php';
		}
		// I guess not, let's see if we can inherit it?
		elseif (self::$inherit_from != null and file_exists((self::$inherit_from != null ? self::$inherit_from.'/' : '').$file.'.php')) {
			$file = self::$inherit_from.'/'.$file.'.php';
		}
		// No? Well what about the root of the views direcotry?
		elseif(file_exists(APPPATH.'/views/'.$file.'.php')) {
			$file = APPPATH.'/views/'.$file.'.php';
		}
		// Not there either? I'm not sure then..
		else {
			throw new Error("View Error", "Unable to load view '{$file}'", 'HALT');
		}
		
		return $file;
	}
	
	public static function set($var, $val)
	{
		if (is_array($var)) {
			foreach ($var as $vr => $vl) {
				self::$vars[$vr] = $vl;
			}
		} else {
			self::$vars[$var] = $val;
		}
	}
	
	public static function vars()
	{
		return self::$vars;
	}
}