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
require APPPATH . '/libraries/scm.php';

require SYSPATH . '/base.php';
require APPPATH . '/libraries/locale.php';
require APPPATH . '/libraries/fishhook.php';

Database::init();

// Load the localization class
$locale = Locale::load(settings('locale'));

// Load the plugins
$plugins = Database::connection()->select('file')->from('plugins')->exec()->fetch_all();
foreach ($plugins as $plugin)
{
	$path = APPPATH . "/plugins/{$plugin['file']}.plugin.php";
	if (file_exists($path)) {
		require $path;
		$plugin = 'Plugin_' . $plugin['file'];
		$plugin = $plugin::init();
	}
}
unset($plugins, $plugin);