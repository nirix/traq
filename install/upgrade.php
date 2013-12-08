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
require __DIR__ . '/helpers/upgrade/v3x.php';
require __DIR__ . '/helpers/fixes.php';

use Installer\Helpers\Upgrade\v3x as v3xUpgrades;
use Installer\Helpers\Fixes;

// Framework libraries
use avalon\Database;
use avalon\output\View;

// Models
use traq\models\User;
use traq\models\Setting;
use traq\models\Ticket;
use traq\models\CustomFieldValue;

// Set page and title
View::set('page', 'upgrade');
View::set('page_title', 'Upgrade');

// Make sure the config file exists...
if (!file_exists('../vendor/traq/config/database.php')) {
    Error::halt('Error', 'Config file not found.');
}

// Get database connection
$db = get_connection();
define('DB_VER', Setting::find('db_version')->value);

// Database revisions
$revisions = array(
    // 3.x revisions
    '3.x' => v3xUpgrades::revisions()
);

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

    // 3.x upgrades
    foreach ($revisions['3.x'] as $revision) {
        if (DB_VER < $revision) {
            // call_user_method_array("v{$revision}", "v3xUpgrades", array($db));
            $method = "v{$revision}";
            v3xUpgrades::{$method}($db);
        }
    }

    // Update database version setting
    $db_ver = Setting::find('setting', 'db_version');
    $db_ver->value = TRAQ_VER_CODE;
    $db_ver->save();

    render('upgrade/complete');
});
