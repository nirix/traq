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
 * HTML Helper
 * @package Meridian
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