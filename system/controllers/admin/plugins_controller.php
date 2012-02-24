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

require __DIR__ . '/base.php';

/**
 * Admin Plugins controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminPluginsController extends AdminBase
{
	public function action_index()
	{
		$this->title(l('plugins'));

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
		if (is_dir($plugins_dir)) {
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
					$plugins['disabled'][] = array_merge($class::info(), array('file' => $match[1]));
				}
				// It's enabled, only call the info() method.
				else
				{
					$class = "Plugin_{$match[1]}";
					$plugins['enabled'][] = array_merge($class::info(), array('file' => $match[1]));
				}
			}
		}
		
		View::set('enabled_plugins', $enabled_plugins);
		View::set('plugins', $plugins);
	}
	
	/**
	 * Enables the specified plugin.
	 *
	 * @param string $file The plugin filename (without .plugin.php)
	 */
	public function action_enable($file)
	{
		$this->db->insert(array('file' => $file))->into('plugins')->exec();
		Request::redirect(Request::base('/admin/plugins'));
	}
	
	/**
	 * Disables the specified plugin.
	 *
	 * @param string $file The plugin filename (without .plugin.php)
	 */
	public function action_disable($file)
	{
		$this->db->delete()->from('plugins')->where('file', $file)->exec();
		Request::redirect(Request::base('/admin/plugins'));
	}
}