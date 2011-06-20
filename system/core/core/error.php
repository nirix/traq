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
	public static function halt($title, $message)
	{
		@ob_end_clean();
		
		$body = array();
		$body[] = "<blockquote style=\"font-family:'Helvetica Neue', Arial, Helvetica, sans-serif;background:#fbe3e4;color:#8a1f11;padding:0.8em;margin-bottom:1em;border:2px solid #fbc2c4;\">";
		
		if (!$title !== null) {
			$body[] = "	<h1 style=\"margin: 0;\">{$title}</h1>";
		}
		
		$body[] = "	{$message}";
		$body[] = "</blockquote>";
		
		echo implode(PHP_EOL, $body);
		exit;
	}
}