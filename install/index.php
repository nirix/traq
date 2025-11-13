<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack P.
 * Copyright (C) 2012-2025 Traq.io
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

use Avalon\Database;
use Avalon\Output\View;

use Traq\Models\User;
use Traq\Models\Setting;

// Set page and title
View::set('page', 'install');
View::set('page_title', 'Installation');

// Make sure the config file doesn't exist...
if (file_exists('../data/config/database.php')) {
    InstallError::halt('Error', 'Config file already exists.');
}

// Index
get('/', function () {
    View::set('title', 'License Agreement');
    render('index');
});

// Database config
post('/step/1', function () {
    View::set('title', 'Step 1 - Database Details');
    View::set('errors', array());
    render('database_config');
});

// Admin account
post('/step/2', function () {
    // Check for form errors
    $errors = array();
    $fields = array('type');

    switch ($_POST['type']) {
        case 'mysql':
        case 'postgresql':
            $fields = array_merge($fields, array('host', 'username', 'database'));
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
    // Make sure there's no Traq installed here
    else if (false && is_installed(array_merge(array('driver' => 'pdo'), $_POST))) {
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
        }

        // Remote database info from _POST
        unset($_POST['type'], $_POST['host'], $_POST['username'], $_POST['password'], $_POST['database']);

        View::set('title', 'Step 2 - Admin Account');
        View::set('errors', []);
        render('admin_account');
    }
});

// Create tables, insert data
post('/step/3', function () {
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
        /** @var \Avalon\Database\PDO $conn */
        $conn = Database::factory($_SESSION['db'], 'main');

        $fileHandle = fopen('./install.sql', 'r');
        if (!$fileHandle) {
            InstallError::halt('Error', 'Failed to open install.sql file.');
        }

        try {
            // Loop through each line of install.sql
            $query = '';
            while (($line = fgets($fileHandle)) !== false) {
                // Skip comments and empty lines
                if (trim($line) === '' || str_starts_with($line, '--') || str_starts_with($line, '/*') || str_starts_with($line, '#')) {
                    continue;
                }

                // Add this line to the current query buffer
                $query .= $line;

                // Check if this line is the end of a query
                if (str_ends_with(trim($line), ';')) {
                    // Execute the query
                    $conn->exec($query);

                    // Reset the query buffer
                    $query = '';
                }
            }

            // Close the file
            fclose($fileHandle);
        } catch (\Exception $e) {
            InstallError::halt('Error', 'Failed to execute install.sql file: ' . $e->getMessage());
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
        if (!file_exists('../data/config/database.php') && is_writable('../data/config')) {
            $handle = fopen('../data/config/database.php', 'w+');
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
