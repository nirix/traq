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

		$plugins = array(
			'enabled' => array(),
			'disabled' => array()
		);

		foreach ($this->db->select()->from('plugins')->order_by('enabled', 'ASC')->exec()->fetch_all() as $plugin)
		{
			$plugins[$plugin['enabled'] ? 'enabled' : 'disabled'][$plugin['file']] = array_merge($plugin, array('installed' => true));
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
				if (!isset($plugins['enabled'][$match[1]]))
				{
					require $plugins_dir . "{$match[1]}.plugin.php";
					$class = "Plugin_{$match[1]}";
					$plugins['disabled'][$match[1]] = array_merge(
						$class::info(),
						array(
							'installed' => isset($plugins['disabled'][$match[1]]),
							'enabled' => false,
							'file' => $match[1]
						)
					);
				}
				// It's enabled, only call the info() method.
				else
				{
					$class = "Plugin_{$match[1]}";
					$key = isset($plugins['enabled'][$match[1]]) ? 'enabled' : 'disabled';
					$plugins[$key][$match[1]] = array_merge($class::info(), $plugins[$key][$match[1]]);
				}
			}
		}

		View::set('plugins', $plugins);
	}
	
	/**
	 * Enables the specified plugin.
	 *
	 * @param string $file The plugin filename (without .plugin.php)
	 */
	public function action_enable($file)
	{
		$file = htmlspecialchars($file);
		require APPPATH . "/plugins/{$file}.plugin.php";
		
		$class_name = "Plugin_{$file}";
		$class_name::__enable();
		
		$plugin = Plugin::find('file', $file);
		$plugin->set('enabled', 1);
		$plugin->save();
		
		Request::redirect(Request::base('/admin/plugins'));
	}
	
	/**
	 * Disables the specified plugin.
	 *
	 * @param string $file The plugin filename (without .plugin.php)
	 */
	public function action_disable($file)
	{
		$file = htmlspecialchars($file);
		
		$class_name = "Plugin_{$file}";
		$class_name::__disable();
		
		$plugin = Plugin::find('file', $file);
		$plugin->set('enabled', 0);
		$plugin->save();
		
		Request::redirect(Request::base('/admin/plugins'));
	}
	
	/**
	 * Installs the specified plugin.
	 *
	 * @param string $file The plugin filename (without .plugin.php)
	 */
	public function action_install($file)
	{
		$file = htmlspecialchars($file);
		require APPPATH . "/plugins/{$file}.plugin.php";
		
		$class_name = "Plugin_{$file}";
		$class_name::__install();
		
		$plugin = new Plugin(array('file' => $file));
		$plugin->set('enabled', 1);
		$plugin->save();
		
		Request::redirect(Request::base('/admin/plugins'));
	}
	
	/**
	 * Uninstalls the specified plugin.
	 *
	 * @param string $file The plugin filename (without .plugin.php)
	 */
	public function action_uninstall($file)
	{
		$file = htmlspecialchars($file);
		
		$class_name = "Plugin_{$file}";
		if (class_exists($class_name))
			$class_name::__uninstall();
		
		$plugin = Plugin::find('file', $file);
		$plugin->delete();
		
		Request::redirect(Request::base('/admin/plugins'));
	}
}