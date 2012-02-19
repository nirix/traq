<?php
/*
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
 * Returns the value of the requested setting.
 *
 * @param string $setting The setting to fetch
 *
 * @return string
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function settings($setting) {
	static $CACHE = array();
	
	if (isset($CACHE[$setting])) {
		return $CACHE[$setting];
	}
	
	$data = Setting::find($setting);
	
	$CACHE[$setting] = $data->value;
	return $CACHE[$setting];
}

/**
 * Returns the value of the requested localization string.
 *
 * @return string
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function l()
{
	return call_user_func_array(array('Locale_' . settings('locale'), 'translate'), func_get_args());
}

/**
 * Returns the localized date.
 *
 * @param string $format
 * @param mixed $timestamo
 *
 * @return string
 */
function ldate()
{
	return call_user_func_array(array('Locale_' . settings('locale'), 'date'), func_get_args());
}

/**
 * Formats the supplied text.
 *
 * @param string $text
 * @param bool $strip_html Disables HTML, making it safe.
 *
 * @return string
 */
function format_text($text, $strip_html = true)
{
	$text = $strip_html ? htmlspecialchars($text) : $text;
	
	FishHook::run('function:format_text', &$text);
	
	return $text;
}

/**
 * Checks if the given regex matches the request
 *
 * @param string $uri
 *
 * @return bool
 */
function active_nav($uri)
{
	$uri = str_replace(
		array(':slug', ':any', ':num'),
		array('([a-zA-Z0-9\-\_]+)', '(.*)', '([0-9]+)'),
		$uri
	);
	return preg_match("#^{$uri}$#", Request::url());
}

/**
 *
 *
 *
 */
function current_user()
{
	return Avalon::app()->user;
}

/**
 * Checks the condition and returns the respective value.
 *
 * @param bool $condition
 * @param mixed $true
 * @param mixed $false
 *
 * @return mixed
 */
function iif($condition, $true, $false = null)
{
	return $condition ? $true : $false;
}

/**
 * Checks if the specified field is a projet or not.
 *
 * @param mixed $find Value [or column] to search for.
 * @param mixed $field Column [or value].
 *
 * @return object
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function is_project($find, $field = 'slug') {
	if ($project = Project::find($field, $find)) {
		return $project;
	} else {
		return false;
	}
}

/**
 * Ticket columns
 *
 * @return array
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function ticket_columns() {
	$columns = array(
		'ticket_id',
		'summary',
		'status',
		'owner',
		'type',
		'component',
		'milestone'
	);
	return $columns;
}

/**
 * Ticket filters
 *
 * @return array
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function ticket_filters() {
	$filters = array(
		'milestone',
		'status',
		'type',
		'component',
	);
	return $filters;
}

/**
 * Calculates the percent of two numbers,
 * if both numbers are the same, 100(%) is returned.
 *
 * @param integer $min Lowest number
 * @param integer $max Highest number
 *
 * @return integer
 */
function get_percent($min, $max)
{
	// Make sure we don't divide by zero
	// and end the entire universe
	if ($min == 0 and $max == 0) return 0;

	// We're good, calcuate it like a boss,
	// toss out the crap we dont want.
	$calculate = ($min/$max*100);
	$split = explode('.',$calculate);

	// Return it like a pro.
	return $split[0];
}