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
	 * Loads the specified locale.
	 *
	 * @param string $locale
	 *
	 * @return object
	 */
	public static function load($locale)
	{
		$file_path = APPPATH . "/locale/{$locale}.php";

		// Check if the file exists..
		if (file_exists($file_path))
		{
			$class = "Locale_{$locale}";

			// Make sure the class isn't loaded already
			if (!class_exists($class))
			{
				require $file_path;
			}

			return new $class();
		}

		return false;
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
	public function translate()
	{
		$string = func_get_arg(0);
		$vars = array_slice(func_get_args(), 1);

		return static::_compile_string(static::get_string($string), $vars);
	}

	/**
	 * Date localization method
	 */
	public function date($format, $timestamp = null)
	{
		return Time::date($format, $timestamp);
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
	 * Determines which replacement to use for plurals.
	 *
	 * @param integer $numeral
	 *
	 * @return integer
	 */
	public static function calculate_numeral($numeral)
	{
		return ($numeral > 1 or $numeral < -1 or $numeral == 0) ? 1 : 0;
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
		if (preg_match_all("/{plural:(?<value>-{0,1}\d+)(,|, ){(?<replacements>.*?)}}/i", $translation, $matches))
		{
			foreach($matches[0] as $id => $match)
			{
				// Split the replacements into an array.
				// There's an extra | at the start to allow for better matching
				// with values.
				$replacements = explode('|', $matches['replacements'][$id]);

				// Get the value
				$value = $matches['value'][$id];

				// Check what replacement to use...
				$replacement_id = static::calculate_numeral($value);
				//die(gettype($replacement_id));
				if ($replacement_id !== false)
				{
					$translation = str_replace($match, $replacements[$replacement_id], $translation);
				}
				// Get the last value then
				else
				{
					$translation = str_replace($match, end($replacements), $translation);
				}
			}
		}

		// We're done here.
		return $translation;
	}
}