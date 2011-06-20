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
	public static $params = array();
	public static $method_params = array();
	private static $routes = array();
	
	public static function process($request)
	{
		$request = '/' . $request;
		require_once APPPATH . '/config/routes.php';
		
		if ($request == '/') {
			static::set_request(static::$routes['root']);
			return true;
		}
		
		if (isset(static::$routes[$request])) {
			static::set_request(static::$routes[$request]);
			return true;
		}
		
		foreach (static::$routes as $route => $args) {
			$route = '#^' . $route . '$#';
			
			if (preg_match($route, $request, $params)) {
				unset($params[0]);
				$args['params'] = array_merge($args['params'], $params);
				$args['value'] = preg_replace($route, $args['value'], $request);
				static::set_request($args);
				return true;
			}
		}
		
		static::set_request(array('value' => 'Error::404', 'params' => array()));
		return false;
	}

	public static function add($route, $value, $params = array())
	{
		static::$routes[$route] = array(
			'template' => $route,
			'value' => $value,
			'params' => $params
		);
	}
	
	private static function set_request($route)
	{
		$bits = explode('/', $route['value']);
		static::$params = $route['params'];
		static::$method_params = array_slice($bits, 1);
		
		$bits = explode('::', $bits[0]);
		if (count($bits) == 3) {
			static::$namespace = $bits[0];
			static::$controller = $bits[1];
			static::$method = $bits[2];
		} else {
			static::$controller = $bits[0];
			static::$method = $bits[1];
		}
	}
}