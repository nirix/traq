<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

// MySQL connection
return array(
    'driver'   => 'PDO',       // Leave as is.
    'type'     => 'MySQL',     // Database type.
    'host'     => 'localhost', // Database server.
    'username' => 'root',      // Database username.
    'password' => 'root',      // Database password.
    'database' => 'traq',      // Database name.
    'prefix'   => ''           // Table prefix, recommended to leave blank.
    //'port'     => 3306         // Database server port
);
