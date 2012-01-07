<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
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
function l() {
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
	$project = Project::find($field, $find);
	
	if ($project->name) {
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