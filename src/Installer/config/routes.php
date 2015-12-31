<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
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

Router::root("Traq\\Installer\\Controllers\\Checks::licenseAgreement");

// Database
Router::post('database_info', '/step/1', 'Traq\\Installer\\Controllers\\Steps::databaseInformation');

// Admin account
Router::post('account_info', '/step/2', 'Traq\\Installer\\Controllers\\Steps::accountInformation');

// Confirm information
Router::post('confirm', '/confirm', 'Traq\\Installer\\Controllers\\Steps::confirmInformation');

// Install
Router::post('install', '/install', 'Traq\\Installer\\Controllers\\Install::install');
