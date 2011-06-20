<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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
 * @return string
 * @package traq
 */
function settings($setting)
{
	static $CACHE;
	
	if (isset($CACHE[$setting])) {
		return $CACHE[$setting];
	}
	
	$data = Setting::find($setting);
	
	$CACHE[$setting] = $data->value;
	return $CACHE[$setting];
}

/**
 * Returns the value of the requested localization string.
 * @return string
 * @package traq
 * @subpackage locale
 */
function l()
{
	static $lang = null;
	
	if ($lang === null) {
		$locale = settings('locale');
		$locale_func = $locale . '_locale';
		require APPPATH . '/locale/' . $locale . '.php';
		$lang = $locale_func();
	}
	
	if (!isset($lang[func_get_arg(0)])) {
		return func_get_arg(0);
	}
	
	$string = $lang[func_get_arg(0)];
	$vars = array_slice(func_get_args(), 1);
	
	// Loop through the vars and replace the the {x} stuff
	$v = 0;
	foreach ($vars as $var) {
		++$v;
		$string = str_replace('{'.$v.'}', $var, $string);
	}
	
	return $string;
}

/**
 * Checks if the specified field is a projet or not.
 * @return mixed
 * @package traq
 */
function is_project($find, $field = 'slug') {
	$project = Project::find($field, $find);
	
	if (isset($project->name)) {
		return $project;
	} else {
		return false;
	}
}