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

use avalon\output\View;
use avalon\Database;

/**
 * Utilises Avalon's view class to render the specified view.
 *
 * @param string $view
 *
 * @return string
 */
function render($view)
{
    echo View::get('layout', array('output' => View::get($view)));
}

/**
 * Returns the opening tag for a form.
 *
 * @param string $url
 *
 * @return string
 */
function form($url)
{
    echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . $url . '" method="post">';
}

/**
 * Connects to the database and returns the link.
 *
 * @return object
 */
function get_connection()
{
    static $conn;

    if ($conn !== null) {
        return $conn;
    }

    require '../vendor/traq/config/database.php';
    $conn = Database::factory($db, 'main');
    return $conn;
}

/**
 * Runs the query.
 *
 * @param string $query
 */
function run_query($query)
{
    $db = get_connection();
    return $db->query(str_replace('traq_', $db->prefix, $query));
}

/**
 * Checks if Traq is already installed.
 *
 * @return bool
 */
function is_installed(array $config)
{
    global $conn;

    // Connect to the database
    $conn = Database::factory($config, 'main');

    // Check if the settings table exists...
    if ($conn->select('value')->from('settings')->exec()) {
        return true;
    }

    return false;
}
