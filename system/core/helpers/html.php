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
		return '<link href="'.$path.'" media="'.$media.'" rel="stylesheet" type="text/css" />'.PHP_EOL;
	}
	
	/**
	 * Returns the code to include a JavaScript file.
	 * @param string $file The path to the JavaScript file.
	 */
	public static function js_inc($path)
	{
		return '<script src="'.$path.'" type="text/javascript"></script>'.PHP_EOL;
	}
	
	/**
	 * Returns the code for a link.
	 * @param string $url The URL.
	 * @param string $label The label.
	 * @param array $options Options for the URL code (class, title, etc).
	 */
	public static function link($url, $label, array $args=array())
	{
		return '<a href="'.$url.'"'.(isset($args['class']) ? ' class="'.$args['class'].'"' :'').'>'.$label.'</a>';
	}
}