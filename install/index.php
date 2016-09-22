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

require './bootstrap.php';

use avalon\Database;
use avalon\output\View;

use traq\models\User;
use traq\models\Setting;

// Set page and title
View::set('page', 'install');
View::set('page_title', 'Installation');

// Make sure the config file doesn't exist...
if (file_exists('../vendor/traq/config/database.php')) {
    InstallError::halt('Error', 'Config file already exists.');
}

// Index
get('/', function(){
    View::set('title', 'License Agreement');
    render('index');
});

// Database config
post('/step/1', function(){
    View::set('title', 'Step 1 - Database Details');
    View::set('errors', array());
    render('database_config');
});

// Admin account
post('/step/2', function(){
    // Check for form errors
    $errors = array();
    $fields = array('type');

    switch ($_POST['type']) {
        case 'mysql':
        case 'postgresql':
                $fields = array_merge($fields, array('host', 'username', 'database'));
            break;

        case 'sqlite':
                $fields[] = 'path';
            break;
    }

    foreach ($fields as $field) {
        if ($_POST[$field] == '') {
            $errors[$field] = true;
        }
    }

    View::set('errors', $errors);

    // Fix the errors
    if (count($errors)) {
        View::set('title', 'Step 1 - Database Details');
        render('database_config');
    }
    // Make sure there's no Traq installed here with the same table prefix
    else if (false and is_installed(array_merge(array('driver' => 'pdo'), $_POST))) {
        InstallError::halt('Error', 'Traq is already installed.');
    }
    // Confirm details
    else {
        // Store DB info in the session
        switch ($_POST['type']) {
            case 'mysql':
            case 'postgresql':
                $_SESSION['db'] = array(
                    'driver'   => 'pdo',
                    'type'     => $_POST['type'],
                    'host'     => $_POST['host'],
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'database' => $_POST['database']
                );
                break;

            case 'sqlite':
                $_SESSION['db'] = array(
                    'driver' => 'pdo',
                    'type'   => 'sqlite',
                    'path'   => $_POST['path']
                );
                break;
        }

        $_SESSION['db']['prefix'] = $_POST['prefix'];


        // Remote database info from _POST
        unset($_POST['type'], $_POST['host'], $_POST['username'], $_POST['password'], $_POST['database'], $_POST['prefix']);

        View::set('title', 'Step 2 - Admin Account');
        View::set('errors', array());
        render('admin_account');
    }
});

// Create tables, insert data
post('/step/3', function(){
    // Check for form errors
    $errors = array();
    foreach (array('username', 'name', 'password', 'email') as $field) {
        if ($_POST[$field] == '') {
            $errors[$field] = true;
        }
    }

    // Fix the errors
    if (count($errors)) {
        View::set('title', 'Step 2 - Admin Account');
        View::set('errors', $errors);
        render('admin_account');
    }
    // Setup the database
    else {
        $conn = Database::factory($_SESSION['db'], 'main');

        // Fetch the install SQL.
        $install_sql = file_get_contents('./install.sql');
        $install_sql = str_replace('traq_', $_SESSION['db']['prefix'], $install_sql);
        $queries = explode(';', $install_sql);

        // Run the install queries.
        foreach($queries as $query) {
            if(!empty($query) && strlen($query) > 5) {
                $conn->query($query);
            }
        }

        // Insert admin account
        $admin = new User(array(
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'name'     => $_POST['name'],
            'email'    => $_POST['email'],
            'group_id' => 1,
        ));
        $admin->save();

        // Create anonymous user
        $anon = new User(array(
            'username'   => 'Anonymous',
            'password'   => sha1(microtime() . rand(0, 200) . time() . rand(0, 200)) . microtime(),
            'name'       => 'Anonymous',
            'email'      => 'anonymous' . microtime() . '@' . $_SERVER['HTTP_HOST'],
            'group_id'   => 3,
            'locale'     => 'enUS',
            'options'    => '{"watch_created_tickets":null}',
            'login_hash' => sha1(microtime() . rand(0, 250) . time() . rand(0, 250) . microtime()),
        ));
        $anon->save();

        // Create setting to save anonymous user ID
        $anon_id = new Setting(array(
            'setting' => 'anonymous_user_id',
            'value'   => $anon->id
        ));
        $anon_id->save();

        // Notification from address
        $setting = new Setting(array(
            'setting' => "notification_from_email",
            'value'   => "noreply@{$_SERVER['HTTP_HOST']}"
        ));
        $setting->save();

        // Create DB version setting
        $db_ver = new Setting(array(
            'setting' => 'db_version',
            'value'   => TRAQ_VER_CODE
        ));
        $db_ver->save();

        // Config file
        $config = array();
        $config[] = '<?php';
        $config[] = '$db = array(';
        foreach ($_SESSION['db'] as $key => $val) {
            $config[] = '    \'' . $key . '\' => "' . $val . '",';
        }
        $config[] = ');';
        $config = implode(PHP_EOL, $config);

        // Write the config to file
        if(!file_exists('../vendor/traq/config/database.php') and is_writable('../vendor/traq/config')) {
            $handle = fopen('../vendor/traq/config/database.php', 'w+');
            fwrite($handle, $config);
            fclose($handle);
            $config_created = true;
        }
        // Tell the user how to create the config file
        else {
            View::set('config_code', $config);
            $config_created = false;
        }

        View::set('config_created', $config_created);
        View::set('title', $config_created ? 'Complete' : 'Config File');
        render('done');
    }
});
