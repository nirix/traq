<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * HTTP Request class
 * @package Avalon
 */
class Request
{
	private static $url;
	private static $segments;
	private static $requested_with;
	public static $request;
	
	public static function process()
	{
		static::$url = trim(static::_get_uri(), '/');
		static::$segments = explode('/', trim(static::$url, '/'));
		static::$requested_with = @$_SERVER['HTTP_X_REQUESTED_WITH'];
		static::$request = $_REQUEST;
	}
	
	public static function base()
	{
		return str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) . (func_num_args() > 0 ? implode('/' , func_get_args()) : '');
	}
	
	public static function redirect($url)
	{
		header("Location: " . $url);
	}
	
	public static function url()
	{
		return static::$url;
	}
	
	public static function seg($num)
	{
		return @static::$segments[$num];
	}
	
	public static function is_ajax()
	{
		return strtolower(static::$requested_with) == 'xmlhttprequest';
	}
	
	private static function _get_uri()
	{
		// Check if there is a PATH_INFO variable
		// Note: some servers seem to have trouble with getenv()
		$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
		if (trim($path, '/') != '' && $path != "/index.php") {
			return $path;
		}
		
		// Check if ORIG_PATH_INFO exists
		$path = str_replace($_SERVER['SCRIPT_NAME'], '', (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO'));
		if (trim($path, '/') != '' && $path != "/index.php") {
			return $path;
		}
		
		// Check for ?uri=x/y/z
		if (isset($_REQUEST['url'])) {
			return $_REQUEST['url'];
		}
		
		// Check the _GET variable
		if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != '') {
			return key($_GET);
		}
		
		// Check for QUERY_STRING
		$path = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
		if (trim($path, '/') != '') {
			return $path;
		}
		
		// Check for REQUEST_URI
		$path = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);
		if (trim($path, '/') != '' && $path != "/index.php") {
			return str_replace(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), '', $path);
		}
		
		// I dont know what else to try, screw it..
		return '';
	}
}