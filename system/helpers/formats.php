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

Load::helper('array');

/**
 * Returns the json encoded version of the passed data.
 *
 * @param mixed $data
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

	if (!is_array($data))
	{
		$data = array($data);
	}

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

	// Remove the parts we don't want...
	if (isset($options['hide']))
	{
		$bits = array_remove_keys($bits, $options['hide']);
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

	// Mime Types, because, you know....
	$mime_types = array(
		'json' => 'application/json',
		'css'  => 'text/css',
		'js'   => 'text/javascript',
		'rss'  => 'application/rss+xml',
		'xml'  => 'application/xml',
	);

	// Check if it's in the allowed mime types array
	if (isset($mime_types[$extension]))
	{
		return $mime_types[$extension];
	}

	// These are the files we want to force to be
	// plain text, we don't want them running on the server.
	$plain_text = array(
		'rb',  // Ruby, my favorite, so it's at the top
		'php', // PHP, not my favorite, should be at the bottom
		'pl',  // Perl
		'py',  // Python
		'h',   // Header file
		'c',   // C file
		'cpp'  // C++ file
	);

	// Check if its in the plain text array
	if (isset($plain_text[$plain_text]))
	{
		return 'text/plain';
	}

	// Unknown extension, at least at this time,
	// or as Leonard McCoy would say, "Damn it Jim, I'm a Doctor not a File extension!".
	return false;
}