<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * Avalon Error class
 * @package Avalon
 */
class Error
{
	private $title;
	private $message;
	
	public function __construct($title, $message, $action = null)
	{
		$this->title = $title;
		$this->message = $message;
		
		switch($action) {
			case 'HALT':
				echo $this->display();
				exit;
			break;
		}
	}
	
	public function display()
	{
		@ob_end_clean();
		
		$body = array();
		$body[] = "<blackquote style=\"font-family: arial; font-size: 14px; border: 1px solid red; width: 80%; margin: 0 auto; background: #f4f4f4; padding: 10px; display: block;\">";
		
		if (!$this->title !== null) {
			$body[] = "	<h1 style=\"margin: 0;\">{$this->title}</h1>";
		}
		
		$body[] = "	{$this->message}";
		$body[] = "</blackquote>";
		
		return implode(PHP_EOL, $body);
	}
	
	public function __toString()
	{
		return $this->message;
	}
}