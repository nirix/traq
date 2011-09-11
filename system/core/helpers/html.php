<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * HTML Helper
 * @package Avalon
 * @subpackage Helpers
 */
class HTML
{
	/**
	 * Returns the code to include a CSS file.
	 * @param string $file The path to the CSS file.
	 */
	public static function css_inc($path, $media = 'screen')
	{
		return '<link href="' . $path . '" media="' . $media . '" rel="stylesheet" type="text/css" />' . PHP_EOL;
	}
	
	public static function cssless_inc($path)
	{
		return '<link href="' . $path . '" rel="stylesheet/less" type="text/css">' . PHP_EOL;
	}
	
	/**
	 * Returns the code to include a JavaScript file.
	 * @param string $file The path to the JavaScript file.
	 */
	public static function js_inc($path)
	{
		return '<script src="' . $path.'" type="text/javascript"></script>' . PHP_EOL;
	}
	
	public static function link($url, $text)
	{
		return '<a href="' . $url . '">' . $text . '</a>';
	}
}