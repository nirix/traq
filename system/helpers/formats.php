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
 * Returns the json encoded version of the passed array.
 *
 * @param array $data
 * @param array $options
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 * @subpackage Helpers
 */
function to_json($data, $options = array())
{
	$bits = array();

	foreach ($data as $bit)
	{
		// Check if the bit is an object and can be turned into an array
		if (is_object($bit) and method_exists($bit, '__toArray'))
		{
			$bits[] = $bit->__toArray();
		}
		// Just throw it into the bits array and
		// let the json_encode fuction handle it
		else
		{
			$bits[] = $bit;
		}
	}

	return json_encode($bits);
}

/**
 * Returns the mime type for the specified extension.
 *
 * @param string $extension
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 * @subpackage Helpers
 */
function mime_type_for($extension)
{
	// Remove the first dot from the extension
	if ($extension[0] == '.')
	{
		$extension = substr($extension, 1);
	}

	$mime_types = array(
		'json' => 'application/json',
		'css'  => 'text/css',
		'js'   => 'text/javascript',
		'rss'  => 'application/rss+xml',
		'xml'  => 'application/xml',
	);

	switch ($extension)
	{
		// Let's force these as plain text
		case 'rb':  // Ruby
		case 'php': // PHP
		case 'pl':  // Perl
		case 'py':  // Python
		case 'h':   // Header file
		case 'c':   // C file
		case 'cpp': // C++ File
			return "text/plain";
			break;
		
		// Other
		default:
			return isset($mime_types[$extension]) ? $mime_types[$extension] : false;
			break;
	}
}