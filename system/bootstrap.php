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

define("SYSPATH", dirname(__FILE__) . '/avalon');
define("APPPATH", dirname(__FILE__));
define("DOCROOT", dirname(dirname(__FILE__)));

// Load common functions and version file
require APPPATH . '/common.php';
require APPPATH . '/version.php';

require SYSPATH . '/base.php';
require APPPATH . '/libraries/locale.php';

// Check for the config file
if (!file_exists(APPPATH . '/config/database.php'))
{
	// No config file, redirect to installer
	header("Location: " . Request::base() . "install");
}

Database::init();

// Load the localization class
$locale = Locale::load(settings('locale'));

// Load the plugins
require APPPATH . '/libraries/plugin_base.php';
$plugins = Database::connection()->select('file')->from('plugins')->where('enabled', '1')->exec()->fetch_all();
foreach ($plugins as $plugin)
{
	$path = APPPATH . "/plugins/{$plugin['file']}.plugin.php";
	
	// Check if the file exists
	if (file_exists($path))
	{
		require $path;

		// Register the path to check for controllers and views
		Load::register_path(APPPATH . "/plugins/{$plugin['file']}");

		// Initiate the plugin
		$plugin = 'Plugin_' . $plugin['file'];
		$plugin = $plugin::init();
	}
}
unset($plugins, $plugin);