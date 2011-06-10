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
 * URL Router
 * @package Meridian
 */
class Router
{
	private static $routes;
	public static $namespace;
	public static $controller;
	public static $method;
	public static $args = array();
	
	public static function connect($template, $params)
	{
		if(is_string($params))
		{
			$params = array($params, 'args' => array());
		}
		
		static::$routes[] = array(
			'template' => $template,
			'route' => $params[0],
			'args' => $params['args']
		);
	}
	
	public static function process($request)
	{
		$request = '/' . (Request::$extension !== null ? str_replace('.' . Request::$extension, '', $request) : $request);
		
		$replace = array(
			':controller' => '.+',
			':action' => '.+',
			':any' => '.+',
			':num' => '[0-9]+'
		);
		
		foreach (static::$routes as $route) {
			if ($route['template'] == $request) {
				return static::set_request($route);
			}
			
			//$route['template'] = trim($route['template'], '/');
			$pattern = str_replace(array_keys($replace), array_values($replace), $route['template']);
			
			if (preg_match('#^' . $pattern . '$#', $request, $matches)) {
				unset($matches[0]);
				
				// Check if the controller/method route contains '$' and assign it
				// the request patterns then clear them from the args array to be
				// passed to the controller method.
				if (preg_match_all('/(?:\$(?P<vals>[^}]))/', $route['route'], $vals)) {
					foreach ($vals['vals'] as $val) {
						$route['route'] = str_replace('$' . $val, $matches[$val], $route['route']);
						unset($matches[$val]);
					}
					$matches = array_merge($matches);
				}
				$route['args'] = array_merge($route['args'], $matches);
				
				return static::set_request($route);
			}
		}
		
		return static::set_request(array(
			'route' => 'Error::notFound',
			'args' => array()
		));
	}
	
	private static function set_request($route)
	{
		$path = explode('::', $route['route']);
		
		if (count($path) >= 3) {
			static::$namespace = $path[0];
			static::$controller = $path[1];
			static::$method = $path[2];
		} else {
			static::$controller = $path[0];
			static::$method = $path[1];
		}
		
		static::$args = $route['args'];
	}
}