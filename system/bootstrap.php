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

define("SYSPATH", dirname(__FILE__) . '/avalon');
define("APPPATH", dirname(__FILE__));
define("DOCROOT", dirname(dirname(__FILE__)));

require SYSPATH . '/base.php';
require APPPATH . '/libraries/fishhook.php';

Database::init();

// Load the plugins
$plugins = Database::driver()->select('file')->from('plugins')->where('is_enabled', 1)->exec()->fetch_all();
foreach ($plugins as $plugin)
{
	require APPPATH . "/plugins/{$plugin['file']}.plugin.php";
	$plugin = 'Plugin_' . $plugin['file'];
	$plugin = $plugin::init();
}
unset($plugins, $plugin);