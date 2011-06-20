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
	public static $args = array();
	private static $routes = array();
	
	/**
	 * Matche the request to a route and get the controller, method and arguments.
	 * @param string $request The request.
	 * @return boolean
	 */
	public static function process($request)
	{
		// Prefix a forward slash to the request.
		$request = '/' . $request;
		
		// Fetch the routes
		require_once APPPATH . '/config/routes.php';
		
		// Are we on the front page?
		if ($request == '/') {
			static::set_request(static::$routes['root']);
			return true;
		}
		
		// Check if we have an exact match
		if (isset(static::$routes[$request])) {
			static::set_request(static::$routes[$request]);
			return true;
		}
		
		// Loop through routes and find a regex match
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
		
		// No match, error controller, make it so.
		static::set_request(array('value' => 'Error::404', 'params' => array()));
		return false;
	}

	/**
	 * Add a route.
	 * @param string $route The route to match to.
	 * @param string $value The controller and method to route to.
	 * @param array $params Parameters to be passed to the controller method.
	 */
	public static function add($route, $value, $params = array())
	{
		static::$routes[$route] = array(
			'template' => $route,
			'value' => $value,
			'params' => $params
		);
	}
	
	/**
	 * Private function to set the routed controller, method, parameters and method arguments.
	 * @param array $route The route array.
	 */
	private static function set_request($route)
	{
		// Seperate the method arguments from the route
		$bits = explode('/', $route['value']);
		static::$params = $route['params'];
		static::$args = array_slice($bits, 1);
		
		// Check if there's a namespace specified
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