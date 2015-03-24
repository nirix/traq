<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
 * https://github.com/nirix
 * https://traq.io
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

use Avalon\Routing\Router;

Router::map(function($r) {
    $r->root("Traq\\Installer\\Controllers\\Checks::licenseAgreement");

    // Database
    $r->post("/step/1", "database_info")->to("Traq\\Installer\\Controllers\\Steps::databaseInformation");

    // Admin account
    $r->post("/step/2", "account_info")->to("Traq\\Installer\\Controllers\\Steps::accountInformation");

    // Confirm information
    $r->post("/confirm", "confirm")->to("Traq\\Installer\\Controllers\\Steps::confirmInformation");

    // Install
    $r->post("/install", "install")->to("Traq\\Installer\\Controllers\\Install::install");
});
