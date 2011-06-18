<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * The core avalon class
 * @package Avalon
 */
class Avalon
{
	private static $version = '0.1';
	private static $app;
	private static $db;
	
	public static function init()
	{
		// Connect to the database
		if (file_exists(APPPATH . 'config/database.php')) {
			
		}
		
		// Route the request
		Request::process();
		Router::process(Request::url());
	}
	
	public static function run()
	{
		// Fetch the AppController
		if (file_exists(APPPATH . '/controllers/app_controller.php')) {
			require_once APPPATH . '/controllers/app_controller.php';
		} else {
			new Error('Avalon::Run Error', 'The app controller could not be loaded.', 'HALT');
		}
		
		// Setup the controller and method info
		$controller_file = strtolower(APPPATH . '/controllers/' . (Router::$namespace != null ? '/' : '') . Router::$controller . '_controller.php');
		$controller_name = Router::$controller . 'Controller';
		$method_view_name = Router::$method;
		$method_name = 'action_' . Router::$method;
		$method_args = Router::$args;
		
		// Check the controller file
		if (!file_exists($controller_file)) {
			$controller_file = APPPATH . '/controllers/error_controller.php';
		}
		
		require_once $controller_file;
		
		// Check the controller and method
		if (!class_exists($controller_name) or !method_exists($controller_name, $method_name)) {
			$controller_file = APPPATH . '/controllers/error_controller.php';
			$controller_name = 'ErrorController';
			$method_name = 'action_404';
			$method_args['request'] = Request::url();
		}
		
		// Start the controller
		static::$app = new $controller_name();
		static::$app->db = static::$db;
		
		// Set the view
		$view = (isset(Router::$namespace) ? Router::$namespace . '/' . Router::$controller . '/' . $method_name : Router::$controller .'/' . $method_view_name);
		if (static::$app->_render['view'] === null) {
			static::$app->_render['view'] = $view;
		}
		
		// Call the method
		call_user_func_array(array(static::$app, $method_name), $method_args);
		
		// Call the 'destructor', why not just use PHP's?
		// even after die or exit is called, the __destruct() is still executed.
		if (method_exists(static::$app, '__shutdown')) {
			static::$app->__shutdown();
		}
	}
	
	public static function app()
	{
		return static::$app;
	}
	
	public static function db()
	{
		return static::$db;
	}
	
	public static function version()
	{
		return static::$version;
	}
}