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
 * Meridian Core
 * @package Meridian
 */
class Meridian
{
	private static $version = '0.2';
	private static $app;
	public static $db;
	
	/**
	 * Loads the necessary files and checks for any errors.
	 */
	public static function init()
	{
		// Load the config files...
		require_once APPPATH.'config/routes.php';
		if(file_exists(APPPATH.'config/database.php')) require_once APPPATH.'config/database.php';
		
		// Process the request and clean _POST, _GET, _COOKIE and _REQUEST arrays.
		Request::process();
		Param::init();
		
		// Route the request
		Router::process(Request::$request);
		
		// Load the controller
		require_once APPPATH . 'controllers/app_controller.php';
		if(file_exists(APPPATH . 'controllers/' . strtolower((Router::$namespace != null ? Router::$namespace . '/' :'').Router::$controller) . '_controller.php'))
			require_once APPPATH . 'controllers/' . strtolower((Router::$namespace != null ? Router::$namespace . '/' :'').Router::$controller) . '_controller.php';
		else
		{
			Router::$controller = 'Error';
			Router::$method = 'notFound';
		}
		
		// Extension check
		if(method_exists(Router::$controller.'Controller', str_replace('.'.Request::$extension, '', Request::$request)))
		{
			Router::$method = str_replace('.'.Request::$extension, '', Request::$request);
		}
		
		// Check if the method exists
		if(!method_exists(Router::$controller . 'Controller', Router::$method))
		{
			Router::$controller = 'Error';
			Router::$method = 'notFound';
		}
		
		if(Router::$controller == 'Error')
			require_once APPPATH.'controllers/error_controller.php';
	}
	
	/**
	 * Starts the app and calls the routed controller and method.
	 */
	public static function run()
	{
		// Initialize the database
		self::$db = Database::init();
		
		// Get the namespace and controller class name
		$namespace = (Router::$namespace != null ? Router::$namespace.'/' :'');
		$class = Router::$controller.'Controller';
		
		// Start the app and call the method
		self::$app = new $class;
		call_user_func_array(array(self::$app, Router::$method), Request::$segments);
		
		// Render the method view and display the output
		View::render((self::$app->_view === null ? $namespace.Router::$controller.'/'.Router::$method : self::$app->_view));
		Output::display(self::$app->_layout);
	}
	
	/**
	 * Returns the app object.
	 */
	public static function app()
	{
		return self::$app;
	}
	
	/**
	 * Displays an error message.
	 * @param string $title
	 * @param string $message
	 */
	public static function error($title, $message)
	{
		@ob_end_clean();
		echo "<!DOCTYPE html>".PHP_EOL;
		echo "<html>".PHP_EOL;
		echo "	<head>".PHP_EOL;
		echo "		<title>{$title}</title>".PHP_EOL;
		echo "		<style type=\"text/css\">".PHP_EOL;
		echo "			body { font-size: 14px; font-family: Verdana, sans-serif; margin: 0; padding: 0; background: #ccc; }".PHP_EOL;
		echo "			header { background: #990000; color: #fff; display: block; padding: 5px; }".PHP_EOL;
		echo "			h1 { margin: 0; }".PHP_EOL;
		echo "			section { display: block; padding: 10px; background: #fff; }".PHP_EOL;
		echo "		</style>".PHP_EOL;
		echo "	</head>".PHP_EOL;
		echo "	<body>".PHP_EOL;
		echo "		<header>".PHP_EOL;
		echo "			<h1>{$title}</h1>".PHP_EOL;
		echo "		</header>".PHP_EOL;
		echo "		<section>".PHP_EOL;
		echo "			{$message}".PHP_EOL;
		echo "		</section>".PHP_EOL;
		echo "	</body>".PHP_EOL;
		echo "</html>".PHP_EOL;
		exit;
	}
	
	/**
	 * Returns the version of Meridian.
	 */
	public static function version()
	{
		return self::$version;
	}
}