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
 * Admin Plugins controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminPluginsController extends AppController
{
	public function action_index()
	{
		// Array to hold plugins
		$plugins = array('enabled' => array(), 'disabled' => array());
		
		// Fetch enabled plugins
		$enabled_plugins = array();
		$rows = $this->db->select('file')->from('plugins')->exec()->fetch_all();
		foreach ($rows as $row)
		{
			$enabled_plugins[] = $row['file'];
		}
		
		// Scan the plugin directory
		$plugins_dir = APPPATH . '/plugins/';
		foreach (scandir($plugins_dir) as $file)
		{
			// Make sure its a plugin, not some weird
			// or unwanted file or directory.
			if (!preg_match('#^([a-zA-Z0-9\-_]+).plugin.php$#', $file, $match))
			{
				continue;
			}
			
			// If the plugin isn't enabled, fetch the plugin
			// file and then call the info() method.
			if (!in_array($match[1], $enabled_plugins))
			{
				require $plugins_dir . "{$match[1]}.plugin.php";
				$class = "Plugin_{$match[1]}";
				$plugins['disabled'][] = $class::info();
			}
			// It's enabled, only call the info() method.
			else
			{
				$class = "Plugin_{$match[1]}";
				$plugins['enabled'][] = array_merge($class::info(), array('file' => $match[1]));
			}
		}
		
		View::set('plugins', $plugins);
	}
}