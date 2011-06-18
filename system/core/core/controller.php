<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * The base controller class
 * @package Avalon
 */
class Controller
{
	public $db;
	public $_render = array('layout' => 'default', 'view' => null);
	
	public function __construct()
	{
		// Get the database for easy access
		$this->db = Avalon::db();
		
		// Allow the views to access the app,
		// even though its not good practice...
		View::set('app', $this);
	}
	
	public function __shutdown()
	{
		if (!$this->_render['view']) {
			return;
		}
		
		// Render the view, get the content and clear the output
		View::render($this->_render['view']);
		$output = Output::body();
		Output::clear();
		
		// Set the X-Powered-By header and render the layout with the content
		header("X-Powered-By: Avalon/" . Avalon::version());
		View::render("layouts/{$this->_render['layout']}", array('output' => $output));
		echo Output::body();
	}
}