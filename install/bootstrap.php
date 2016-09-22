<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

define("APPPATH", dirname(__FILE__));
define("SYSPATH", dirname(__DIR__) . '/vendor/avalon');

require '../vendor/traq/version.php';

require SYSPATH . '/autoloader.php';

use avalon\Autoloader;
Autoloader::vendorLocation(dirname(__DIR__) . '/vendor');
Autoloader::register();

require 'nanite.php';
require SYSPATH . '/libs/fishhook.php';
require '../vendor/traq/models/user.php';
require '../vendor/traq/common.php';
require 'common.php';

use avalon\output\View;

session_start();

/**
 * Simple error class
 */
class InstallError
{
    /**
     * Halts the page and displays the error.
     *
     * @param string $title
     * @param string $message
     */
    public static function halt($title, $message)
    {
        // Check if the message is the database complaining about a non existent table
        // and ignore it.
        if ($title == 'Database Error' and preg_match("/Table '(.*)' doesn\'t exist/", $message)) {
            return false;
        }

        @ob_end_clean();
        View::set('title', $title);
        View::set('message', $message);
        die(render('error'));
    }
}
