<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * Content output class
 * @package Avalon
 */
class Output
{
	private static $body = '';
	
	public static function body()
	{
		return static::$body;
	}
	
	public static function append($content)
	{
		static::$body .= $content;
	}
	
	public static function clear()
	{
		static::$body = '';
	}
}