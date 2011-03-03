<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
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
 *
 * $Id$
 */

// Fetch required files
require('common.php');
require('../system/version.php');
include('../system/config.php');
include('../system/libraries/db.class.php');

// Intro
if(!isset($_POST['step']))
{
	// Install checks
	
	// config.php check
	$checks['config.php'] = array(
		'name' => "<code>config.php</code> file",
		'class' => 'good',
		'message' => 'Found'
	);
	if(!file_exists('../system/config.php'))
	{
		$error = true;
		$checks['config.php']['class'] = 'bad';
		$checks['config.php']['message'] = 'Not found';
	}
	
	// cache dir check
	/*$checks['cache_dir'] = array(
		'name' => "<code>cache/</code> directory",
		'class' => 'good',
		'message' => 'Writable'
	);
	if(!is_writable('../cache'))
	{
		$error = true;
		$checks['cache_dir']['class'] = 'bad';
		$checks['cache_dir']['message'] = 'Not writable';
	}*/
	
	// Database check
	$checks['database'] = array(
		'name' => "Database",
		'class' => 'good',
		'message' => 'Connected'
	);
	
	// Check connection
	$connect = mysql_connect($conf['db']['server'],$conf['db']['user'],$conf['db']['pass']);
	if(!$connect)
	{
		$error = true;
		$checks['database']['class'] = 'bad';
		$checks['database']['message'] = 'Cannot connect';
	}
	else
	{
		// Check database
		if(!mysql_select_db($conf['db']['dbname']))
		{
			$error = true;
			$checks['database']['class'] = 'bad';
			$checks['database']['message'] = 'Cannot connect';
		}
		else
		{
			// Make sure Traq isn't already installed.
			$tables = mysql_query("SHOW TABLES");
			while($info = mysql_fetch_array($tables)) {
				if($info['0'] == $conf['db']['prefix'].'settings')
				{
					$error = true;
					$checks['database']['class'] = 'bad';
					$checks['database']['message'] = 'Traq already installed';
				}
			}
		}
	}
	
	head('install');
	?>
	<form action="install.php" method="post">
		<input type="hidden" name="step" value="1" />
		
		<table width="400" align="center">
		<?php foreach($checks as $check) { ?>
			<tr>
				<td><?php echo $check['name']?></td>
				<td class="<?php echo $check['class']?>" align="right"><?php echo $check['message']?></td>
			</tr>
		<?php } ?>
		</table>
		
		<?php if(!@$error) { ?>
			<div align="center"><input type="submit" value="Next" /></div>
		<?php } ?>
	</form>
	<?php
	foot();
}
// Step One
elseif($_POST['step'] == '1')
{	
	// Check that Traq is not already installed on the Database.
	mysql_connect($conf['db']['server'],$conf['db']['user'],$conf['db']['pass']);
	mysql_select_db($conf['db']['dbname']);
	$tables = mysql_query("SHOW TABLES");
	while($info = mysql_fetch_array($tables)) {
		if($info['0'] == $conf['db']['prefix'].'settings')
		{
			error('Install','Traq already installed');
			exit;
		}
	}
	
	head('install');
	?>
	<form action="install.php" method="post">
		<input type="hidden" name="step" value="2" />
		
		<table width="400" align="center">
			<tr>
				<td>Traq name</td>
				<td><input type="text" name="traq_name" value="Traq" /></td>
			</tr>
			<tr>
				<td>Admin Username</td>
				<td><input type="text" name="admin_name" value="Admin" /></td>
			</tr>
			<tr>
				<td>Admin Password</td>
				<td><input type="password" name="admin_pass" /></td>
			</tr>
			<tr>
				<td>Admin Email</td>
				<td><input type="text" name="admin_email" /></td>
			</tr>
		</table>
		
		<?php if(!@$error) { ?>
			<div align="center"><input type="submit" value="Install" /></div>
		<?php } ?>
	</form>
	<?php
	foot();
}
// Step Two
elseif($_POST['step'] == '2')
{
	// Check for errors in the fields.
	if(empty($_POST['traq_name']))
		$error = true;
	if(empty($_POST['admin_name']))
		$error = true;
	if(empty($_POST['admin_pass']))
		$error = true;
	if(empty($_POST['admin_email']))
		$error = true;
	
	if(@$error)
	{
		head('install');
		?>
		<table width="400" align="center">
			<tr>
				<td align="center" class="bad"><h2>Error</h2>Please fill in all fields.</td>
			</tr>
		</table>
		<?php
		foot();
	}
	else
	{
		// Fetch required files.
		include('../inc/user.class.php');
		define("DBPF",$conf['db']['prefix']);
		
		// Connect to the Database.
		$db = new Database($conf['db']['server'],$conf['db']['user'],$conf['db']['pass'],$conf['db']['dbname']);
		
		// Fetch the install SQL.
		$installsql = file_get_contents('install.sql');
		$installsql = str_replace('traq_',$conf['db']['prefix'],$installsql);
		$queries = explode(';',$installsql);
		
		// Run the install queries.
		foreach($queries as $query) {
			if(!empty($query) && strlen($query) > 5) {
				$db->query($query);
			}
		}
		
		$db->query("INSERT INTO `".DBPF."plugins` (`name`, `author`, `website`, `version`, `enabled`, `install_sql`, `uninstall_sql`) VALUES
					('New Line Converter', 'Jack', 'http://traqproject.org', '1.0', 1, '', '');");
		
		$db->query("INSERT INTO `".DBPF."plugin_code` (`plugin_id`, `title`, `hook`, `code`, `execorder`, `enabled`) VALUES
					(".$db->insertid().", 'formattext', 'function_formattext', '".$db->res('$text = nl2br($text);')."', 0, 1);");
		
		// Insert Settings.
		$db->query("UPDATE ".$conf['db']['prefix']."settings SET value='".$db->res($_POST['traq_name'])."' WHERE setting='title'");
		
		// Create Admin User.
		$user = new User;
		$admindata = array(
			'username' => $_POST['admin_name'],
			'password' => $_POST['admin_pass'],
			'password2' => $_POST['admin_pass'],
			'email' => $_POST['admin_email'],
			'name' => $_POST['admin_name'],
			'group_id' => 1
		);
		$user->register($admindata);
		
		head('install');
		?>
		<table width="400" align="center">
			<tr>
				<td align="center" class="good"><h2>Installation Complete</h2>You may now login to the <a href="../admincp/">AdminCP</a> with the username and password you provided.</td>
			</tr>
		</table>
		<?php
		foot();
	}
}
