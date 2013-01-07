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

// MySQL connection
$db = array(
    'driver'   => 'PDO',       // Leave as is.
    'type'     => 'MySQL',     // Database type.
    'host'     => 'localhost', // Database server.
    'username' => 'root',      // Database username.
    'password' => 'root',      // Database password.
    'database' => 'traq',      // Database name.
    'prefix'   => ''           // Table prefix.
    //'port'     => 3306         // Database server port
);

// SQLite connection
/*
$db = array(
    'driver' => 'PDO',
    'type'   => 'SQLite',
    'path'   => '/path/to/database.sqlite'
);
*/

// Other database
// See: http://php.net/manual/en/pdo.drivers.php
/*
$db = array(
    'driver' => 'PDO',
    'dsn'    => 'PDO DSN goes here'
);
*/
