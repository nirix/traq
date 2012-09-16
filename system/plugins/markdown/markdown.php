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
 * Markdown Plugin.
 *
 * @package Traq
 * @subpackage Plugins
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Plugin_markdown extends PluginBase
{
	private static $parser;

	/**
	 * Returns the plugin info.
	 */
	public static function info()
	{
		return array(
			'name' => 'Markdown',
			'version' => '0.1',
			'author' => 'Jack P.'
		);
	}

	/**
	 * Handles the startup of the plugin.
	 */
	public static function init()
	{
		FishHook::add('function:format_text', array('Plugin_markdown', 'format_text'));
	}

	/**
	 * Handles the format_text function hook.
	 */
	public static function format_text(&$text, $strip_html)
	{
		if ($strip_html)
		{
			$text = htmlspecialchars_decode($text);
		}

		// Check if the parser has been loaded...
		if (static::$parser === null)
		{
			// Load the parser
			require __DIR__ . '/libraries/markdown.php';
			static::$parser = new Markdown_Parser;
		}

		// Parse the text
		$text = static::$parser->transform($text);
	}
}