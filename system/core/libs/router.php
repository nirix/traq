<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * Avalon's request router
 * @package Avalon
 */
class Router
{
	public static $namespace;
	public static $controller;
	public static $method;
	public static $args = array();
	private static $routes = array();
	
	public static function process($request)
	{
		require_once APPPATH . '/config/routes.php';
		
		foreach (static::$routes as $route) {
			$regex = static::compile($route['template']);
			
			// Check if it matches
			if (preg_match($regex, $request, $matches)) {
				// Get the params
				$params = array();
				foreach ($matches as $key => $value) {
					if (!is_int($key)) {
						$params[$key] = $value;
					}
				}
				$route['params'] = array_merge($route['params'], $params);
				
				if (strpos($route['value'], '$') !== false) {
					foreach (explode('::', $route['value']) as $bit) {
						if (strpos($bit, '$') !== false) {
							$route['value'] = str_replace($bit, $route['params'][trim($bit, '$')], $route['value']);
							unset($route['params'][trim($bit, '$')]);
						}
					}
				}
				
				static::set_request($route);
				return true;
			}
		}
		
		static::set_request(array('value' => 'Error::404', 'params' => array('request' => $request)));
		return false;
	}
		
	public static function add($route, $value, $params = array())
	{
		static::$routes[] = array(
			'template' => $route,
			'value' => $value,
			'params' => $params
		);
	}
	
	private static function set_request($route)
	{
		$bits = explode('::', $route['value']);
		
		if (count($bits) == 3) {
			static::$namespace = $bits[0];
			static::$controller = $bits[1];
			static::$method = $bits[2];
		} else {
			static::$controller = $bits[0];
			static::$method = $bits[1];
		}
		
		// Remove the controller and action values from the params array
		unset($route['params']['controller'], $route['params']['action']);
		
		static::$args = $route['params'];
	}
	
	private static function compile($uri)
	{
		$expression = preg_replace('#[.\\+*?[^\\]${}=!|]#', '\\\\$0', $uri);
		
		if (strpos($expression, '(') !== false) {
			// Make optional parts of the URI non-capturing and optional
			$expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
		}

		// Insert default regex for keys
		$expression = str_replace(array('<', '>'), array('(?P<', '>[^/.,;?\n]++)'), $expression);
		
		return '#^'.$expression.'$#uD';
	}
}