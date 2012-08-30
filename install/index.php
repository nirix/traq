<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

// Make sure the config file doesn't exist...
if (file_exists('../system/config/database.php'))
{
	Error::halt('Error', 'Config file already exists.');
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
	foreach (array('type', 'host', 'username', 'database') as $field)
	{
		if ($_POST[$field] == '')
		{
			$errors[$field] = true;
		}
	}

	View::set('errors', $errors);

	// Fix the errors
	if (count($errors))
	{
		View::set('title', 'Step 1 - Database Details');
		render('database_config');
	}
	// Make sure there's no Traq installed here with the same table prefix
	else if (false and is_installed(array_merge(array('driver' => 'pdo'), $_POST)))
	{
		Error::halt('Error', 'Traq is already installed.');
	}
	// Confirm details
	else
	{
		// Store DB info in the session
		$_SESSION['db'] = array(
			'driver' => 'pdo',
			'type' => $_POST['type'],
			'host' => $_POST['host'],
			'username' => $_POST['username'],
			'password' => $_POST['password'],
			'database' => $_POST['database'],
			'prefix' => $_POST['prefix'],
		);

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
	foreach (array('username', 'name', 'password', 'email') as $field)
	{
		if ($_POST[$field] == '')
		{
			$errors[$field] = true;
		}
	}

	// Fix the errors
	if (count($errors))
	{
		View::set('title', 'Step 2 - Admin Account');
		View::set('errors', $errors);
		render('admin_account');
	}
	// Setup the database
	else
	{
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
			'name' => $_POST['name'],
			'email' => $_POST['email'],
			'group_id' => 1,
		));
		$admin->save();

		// Config file
		$config = array();
		$config[] = '<?php';
		$config[] = '$db = array(';
		foreach (array('driver', 'type', 'host', 'username', 'password', 'database', 'prefix') as $key)
		{
			$config[] = '	\'' . $key . '\' => "' . $_SESSION['db'][$key] . '",';

		}
		$config[] = ');';
		$config = implode(PHP_EOL, $config);

		// Write the config to file
		if(!file_exists('../system/config/database.php') and is_writable('../system/config'))
		{
			$handle = fopen('../system/config/database.php', 'w+');
			fwrite($handle, $config);
			fclose($handle);
			$config_created = true;
		}
		// Tell the user how to create the config file
		else
		{
			View::set('config_code', $config);
			$config_created = false;
		}

		View::set('config_created', $config_created);
		View::set('title', $config_created ? 'Complete' : 'Config File');
		render('done');
	}
});
