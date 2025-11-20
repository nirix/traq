<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
 * https://github.com/nirix
 * http://traq.io
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
define("DOCROOT", dirname(__DIR__));
define("SYSPATH", DOCROOT . '/vendor/avalon/framework');
define("APPPATH", DOCROOT . '/vendor/traq');
define("DATADIR", DOCROOT . '/data');

use Avalon\Database;
use Avalon\Http\Request;
use Avalon\Output\View;

require DOCROOT . '/src/aliases.php';

// Fetch the routes
require_once DOCROOT . '/src/Traq/Config/routes.php';

// Load common functions and version file
require DOCROOT . '/src/Traq/common.php';
require DOCROOT . '/src/Traq/version.php';

// Check for the database config file
if (!file_exists(DATADIR . '/config/database.php')) {
    // No config file, redirect to installer
    new Request;
    header("Location: " . Request::base('install'));
    exit;
}
// Include config and connect
else {
    require DATADIR . '/config/database.php';
    // If no prefix is set, use an empty string
    if (!isset($db['prefix'])) {
        $db['prefix'] = '';
    } else {
        define('DB_HAS_PREFIX', true);
    }
    Database::init($db);
}

// Set up view paths
View::$searchPaths[] = DATADIR . '/themes/' . settings('theme');
View::$searchPaths[] = DOCROOT . '/src/views/' . settings('theme');
View::$searchPaths[] = DOCROOT . '/src/views';

// Load helpers
require DOCROOT . '/src/Traq/Helpers/uri.php';
require DOCROOT . '/src/Traq/Helpers/ui.php';
require DOCROOT . '/src/Traq/Helpers/formatting.php';
require DOCROOT . '/src/Traq/Helpers/time_ago.php';
require DOCROOT . '/src/Traq/Helpers/errors.php';
require DOCROOT . '/src/Traq/Helpers/subscriptions.php';
require DOCROOT . '/src/Traq/Helpers/tickets.php';

// Load the plugins
$plugins = Database::connection()->select('file')->from('plugins')->where('enabled', '1')->exec()->fetch_all();
$pluginPaths = [
    DATADIR . '/plugins',
    DOCROOT . '/src/Traq/Plugins',
];
foreach ($pluginPaths as $pluginPath) {
    foreach ($plugins as $plugin) {
        // Plugin file plath
        $path = $pluginPath . "/{$plugin['file']}/{$plugin['file']}.php";

        // Check if the file exists
        if (file_exists($path)) {
            require $path;
            $pluginName = get_plugin_name($plugin['file']);

            // Register the path to check for controllers and views
            View::$searchPaths[] = $pluginPath . "/{$plugin['file']}/views";

            // Register the namespace
            $autoloader = require DOCROOT . '/vendor/autoload.php';
            $autoloader->addPsr4("{$pluginName}\\", $pluginPath . "/{$plugin['file']}");

            // Initiate the plugin
            $plugin = "\\traq\plugins\\" . get_plugin_name($plugin['file']);
            $plugin = $plugin::init();
        }
    }
}
unset($plugins, $plugin, $pluginPath, $pluginPaths);

// Load the localization file
$locale = Traq\Locale::load(settings('locale'));
