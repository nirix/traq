<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * JavaScript Helper
 * @package Avalon
 * @subpackage Helpers
 */
class JS
{
	public static function escape($content)
	{
		$replace = array(
			"\r" => '',
			"\n" => ''
		);
		return addslashes(str_replace(array_keys($replace), array_values($replace), $content));
	}
}