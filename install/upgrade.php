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

require __DIR__ . '/bootstrap.php';
require '../vendor/traq/helpers/uri.php';

// Helpers
require __DIR__ . '/helpers/upgrade/base.php';
require __DIR__ . '/helpers/upgrade/v3x.php';
require __DIR__ . '/helpers/fixes.php';

use Installer\Helpers\Upgrade\v3x as v3xUpgrades;
use Installer\Helpers\Fixes;

// Framework libraries
use avalon\Database;
use avalon\output\View;

// Set page and title
View::set('page', 'upgrade');
View::set('page_title', 'Upgrade');

// Make sure the config file exists...
if (!file_exists('../vendor/traq/config/database.php')) {
    InstallError::halt('Error', 'Config file not found.');
}

// Get database connection
$db = get_connection();
$db_ver = $db->query("SELECT * FROM `{$db->prefix}settings` WHERE `setting` = 'db_version' LIMIT 1")->fetch();
define('DB_VER', $db_ver['value']);

// Index
get('/', function(){
    if (DB_VER < TRAQ_DB_VER) {
        render('upgrade/welcome');
    } else {
        render('upgrade/up_to_date');
    }
});

// Upgrade
post('/step/1', function(){
    global $db, $revisions;

    // Traq 3.x upgrades
    v3xUpgrades::run($db, DB_VER);

    // Update database version setting
    $db->query("UPDATE `{$db->prefix}settings` SET `value` = '" . TRAQ_VER_CODE . "' WHERE `setting` = 'db_version' LIMIT 1");

    render('upgrade/complete');
});
