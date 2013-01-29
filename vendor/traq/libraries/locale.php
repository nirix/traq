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

namespace traq\libraries;

use \avalon\core\Load;
use \Time;

Load::helper('array');

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
    protected $locale = array();

    /**
     * Constructor!
     */
    public function __construct()
    {
        // If localization strings are stored in
        // the Locale_x->locale() method, push them
        // to the Locale_x->$locale array.
        if (method_exists($this, 'locale')) {
            $this->locale = $this->locale();
        }
    }

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
        if (file_exists($file_path)) {
            $class = "\\traq\locale\\{$locale}";

            // Make sure the class isn't loaded already
            if (!class_exists($class)) {
                require $file_path;
            }

            $localization = new $class();

            // Load plugin translation files
            foreach (Load::$search_paths as $path) {
                $locale_file = "{$path}/locale/{$locale}.php";
                if (file_exists($locale_file)) {
                    $localization->add(include($locale_file));
                }
            }

            return $localization;
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
    public function translate($string, $vars = array())
    {
        $string = func_get_arg(0);

        if (!is_array($vars)) {
            $vars = array_slice(func_get_args(), 1);
        }

        return $this->_compile_string($this->get_string($string), $vars);
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
    public function get_string($string)
    {
        $locale = &$this->locale;
        $indexes = explode('.', $string);

        // Exact match?
        if (isset($locale[$string])) {
            if(is_array($locale[$string]) && isset($locale[$string][0])) {
                return $locale[$string][0];
            } else {
                return $locale[$string];
            }
        }

        // Loop over the indexes and find the string
        foreach ($indexes as $index) {
            // If this is a single string, but is also an array
            // like "timeline" and "timeline.by_x", check if
            // timeline[0] exists.
            if (isset($locale[$index]) and is_array($locale[$index]) and count($indexes) === 1) {
                return $locale[$index][0];
            }
            // Check if it's a single index.
            elseif (isset($locale[$index]) and !is_array($locale[$index])) {
                return $locale[$index];
            }
            // If this is an array, set it to be
            // searched by the next index in the string.
            elseif (isset($locale[$index]) and is_array($locale[$index])) {
                $locale = &$locale[$index];
            }
            // We didnt find it, return the original.
            else {
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
    public function calculate_numeral($numeral)
    {
        return ($numeral > 1 or $numeral < -1 or $numeral == 0) ? 1 : 0;
    }

    /**
     * Adds extra locale strings
     * If collisions occur, the new string will overwrite the old one.
     *
     * @param array $vars
     */
    public function add($vars)
    {
        $this->locale = array_merge_recursive2($this->locale, $vars);
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
    protected function _compile_string($string, $vars)
    {
        $translation = $string;

        // If $vars isn't an array, get everything after
        // the string and use whatever we get.
        if (!is_array($vars)) {
            $vars = array_slice(func_get_args(), 1);
        }

        // Loop through and replace the placeholders
        // with the values from the $vars array.
        $count = 0;
        foreach ($vars as $key => $val) {
            $count++;

            // If array key is an integer,
            // use the counter to avoid clashes
            // with numbered placeholders.
            if (is_integer($key)) {
                $key = $count;
            }

            // Replace placeholder with value
            $translation = str_replace(array("{{$key}}"), $val, $translation);
        }

        // Match plural:n,{x, y}
        if (preg_match_all("/{plural:(?<value>-{0,1}\d+)(,|, ){(?<replacements>.*?)}}/i", $translation, $matches)) {
            foreach($matches[0] as $id => $match) {
                // Split the replacements into an array.
                // There's an extra | at the start to allow for better matching
                // with values.
                $replacements = explode('|', $matches['replacements'][$id]);

                // Get the value
                $value = $matches['value'][$id];

                // Check what replacement to use...
                $replacement_id = $this->calculate_numeral($value);
                if ($replacement_id !== false) {
                    $translation = str_replace($match, $replacements[$replacement_id], $translation);
                }
                // Get the last value then
                else {
                    $translation = str_replace($match, end($replacements), $translation);
                }
            }
        }

        // We're done here.
        return $translation;
    }
}
