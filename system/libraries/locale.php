<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
 * 
 * This file is part of Traq.
 * 
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Traq Localization System
 * Copyright (C) Jack Polgar
 *
 * @author Jack P.
 * @copyright (C) Jack P.
 * @package Traq
 * @subpackage Locale
 */
class Locale
{
	protected static $info = array();
	protected static $locale = array();

	/**
	 * Initializes the locale class.
	 */
	public static function init()
	{
	}

	/**
	 * Returns the locale information.
	 *
	 * @return array
	 */
	public static function info()
	{
		return static::$info;
	}

	/**
	 * Translates the specified string. 
	 *
	 * @return string
	 */
	public static function translate()
	{
		$string = func_get_arg(0);
		$vars = array_slice(func_get_args(), 1);

		return static::_compile_string(static::get_string($string), $vars);
	}

	/**
	 * Date localization method
	 */
	public static function date($format, $timestamp = null)
	{
		return Time::date($format, $timestamp !== null ? $timestamp : time());
	}

	/**
	 * Fetches the translation for the specified string.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function get_string($string)
	{
		$locale = &static::$locale;
		$indexes = explode('.', $string);

		// Loop over the indexes and find the string
		foreach ($indexes as $index)
		{
			// If this is a single string, but is also an array
			// like "timeline" and "timeline.by_x", check if
			// timeline[0] exists.
			if (isset($locale[$index]) and is_array($locale[$index]) and count($indexes) === 1)
			{
				return $locale[$index][0];
			}
			// Check if it's a single index.
			elseif (isset($locale[$index]) and !is_array($locale[$index]))
			{
				return $locale[$index];
			}
			// If this is an array, set it to be
			// searched by the next index in the string.
			elseif (isset($locale[$index]) and is_array($locale[$index]))
			{
				$locale = &$locale[$index];
			}
			// We didnt find it, return the original.
			else
			{
				return $string;
			}
		}
	}

	/**
	 * Compiles the translated string with the variables.
	 *
	 * @example
	 *     _compile_string('{plural:$1, {$1 post|$1 posts}}', array(1));
	 *     will become "1 post"
	 *
	 * @param string $string
	 * @param array $vars
	 *
	 * @return string
	 */
	protected static function _compile_string($string, $vars)
	{
		$translation = $string;

		// Loop through and replace {x}, ${x} or $x
		// with the values from the $vars array.
		$v = 1;
		foreach ($vars as $var)
		{
			$translation = str_replace(array("{{$v}}", "\${{$v}}", "\${$v}"), $vars[$v - 1], $translation);
			$v++;
		}

		// Match plural:n,{x, y}
		if (preg_match("/{plural:(?<count>[0-9]+)(,|, ){(?<replacements>.*)}}/i", $translation, $matches))
		{
			$replacements = explode('|', $matches['replacements']);
			$count = $matches['count'] - 1;

			// Check if there is a specific value for the count variable.
			if (isset($replacements[$count]))
			{
				$translation = str_replace($matches[0], $replacements[$count], $translation);
			}
			// Get the last value then
			else
			{
				$translation = str_replace($matches[0], $replacements[count($replacements) - 1], $translation);
			}
		}

		// We're done here.
		return $translation;
	}
}