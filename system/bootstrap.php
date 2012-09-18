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

// Define the paths needed
define("SYSPATH", dirname(__FILE__) . '/avalon');
define("APPPATH", dirname(__FILE__));
define("DOCROOT", dirname(dirname(__FILE__)));

// Load the framework
require SYSPATH . '/base.php';
use avalon\Database;
use avalon\core\Load;

// Setup the autoloader
use avalon\Autoloader;
Autoloader::vendor_path(__DIR__ . '/vendor');

// Register the Avalon and Traq namespaces
// to directories outside the vendor directory.
Autoloader::register_namespaces(array(
	'avalon' => SYSPATH,
	'traq' => APPPATH
));

// Alias classes so we dont need to
// have "use ...." in all files.
Autoloader::alias_classes(array(
	'avalon\http\Router' => 'Router',
	'avalon\output\View' => 'View',
	'avalon\http\Request' => 'Request',

	// Helpers
	'avalon\helpers\Time' => 'Time'
));

// Register the autoloader
Autoloader::register();

// Fetch the routes
require_once APPPATH . '/config/routes.php';

// Load common functions and version file
require APPPATH . '/common.php';
require APPPATH . '/version.php';

// Check for the database config file
if (!file_exists(APPPATH . '/config/database.php')) {
	// No config file, redirect to installer
	header("Location: " . Request::base() . "install");
}
// Include config and connect
else {
	require APPPATH . '/config/database.php';
	Database::init($db);
}

// Load the localization file
$locale = traq\libraries\Locale::load(settings('locale'));

// Load the plugins
require APPPATH . '/libraries/plugin_base.php';
$plugins = Database::connection()->select('file')->from('plugins')->where('enabled', '1')->exec()->fetch_all();
foreach ($plugins as $plugin) {
	// Plugin file plath
	$path = APPPATH . "/plugins/{$plugin['file']}/{$plugin['file']}.php";

	// Check if the file exists
	if (file_exists($path)) {
		require $path;

		// Register the path to check for controllers and views
		Load::register_path(APPPATH . "/plugins/{$plugin['file']}");
	}
}

// Initiate the plugins
foreach ($plugins as $plugin) {
	$plugin = 'Plugin_' . $plugin['file'];
	$plugin = $plugin::init();
}
unset($plugins, $plugin);
