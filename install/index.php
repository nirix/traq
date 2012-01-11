<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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

require_once "common.php";
require_once "../system/libraries/db.class.php";

$step = (isset($_POST['step']) ? $_POST['step'] : 1);

// Check if already installed, if not, show licence.
if($step == 1)
{
	head('install', $step);
	
	if(is_installed())
	{
		?><div align="center" class="message error">Traq is already installed.</div><?php
	}
	elseif(file_exists("../system/config.php"))
	{
		?><div align="center" class="message error"><code>system/config.php</code> exists, unable to continue.</div><?php
	}
	else
	{
		?>
		<form action="index.php" method="post">
		<input type="hidden" name="step" value="<?php echo $step+1; ?>" />
			<h2>Licence Agreement</h2>
			<pre id="licence"><?php echo htmlentities(file_get_contents("../COPYING")); ?></pre>
			<div id="actions">
				<input type="submit" value="Accept" />
			</div>
		</div>
		<?php
	}
	foot();
}
// Database info
elseif($step == 2 and !is_installed())
{
	head('install', $step);
	
	// Step 2.1: Check DB connection.
	if(isset($_POST['db']))
	{
		$failed = false;
		
		$link = mysql_connect($_POST['db']['server'], $_POST['db']['user'], $_POST['db']['pass']);
		if(!$link) { $failed = true; }
		if(!mysql_select_db($_POST['db']['dbname'], $link)) { $failed = true; }
		
		if($failed)
		{
			?><div align="center" class="message error">Unable to connect to database.</div><?php
		}
		else
		{
			?>
			<form action="index.php" method="post">
				<input type="hidden" name="step" value="<?php echo $step+1; ?>" />
				<input type="hidden" name="db" value='<?php echo json_encode($_POST['db']); ?>' />
				<div align="center" class="message good">Database connection succeeded.</div>
				<div id="actions">
					<input type="submit" value="Continue" />
				</div>
			</form>
			<?php
		}
	}
	// Database information form.
	else
	{
		if(file_exists("../system/config.php")) { require_once "../system/config.php"; }
		?>
		<form action="index.php" method="post">
			<input type="hidden" name="step" value="<?php echo $step; ?>" />
			<h2>Database Information</h2>
			<table class="inputForm">
				<tr>
					<td class="label">Server</td>
					<td><input type="text" name="db[server]" autocomplete="off" value="<?php echo (isset($conf['db']['server']) ? $conf['db']['server'] : 'localhost')?>" />
				</tr>
				<tr>
					<td class="label">Username</td>
					<td><input type="text" name="db[user]" autocomplete="off" value="<?php echo (isset($conf['db']['user']) ? $conf['db']['user'] : 'root')?>" />
				</tr>
				<tr>
					<td class="label">Password</td>
					<td><input type="text" name="db[pass]" autocomplete="off" value="<?php echo (isset($conf['db']['pass']) ? $conf['db']['pass'] : 'root')?>" />
				</tr>
				<tr>
					<td class="label">Database</td>
					<td><input type="text" name="db[dbname]" autocomplete="off" value="<?php echo (isset($conf['db']['dbname']) ? $conf['db']['dbname'] : 'traq')?>" />
				</tr>
				<tr>
					<td class="label">Prefix</td>
					<td><input type="text" name="db[prefix]" autocomplete="off" value="<?php echo (isset($conf['db']['prefix']) ? $conf['db']['prefix'] : 'traq_')?>" />
				</tr>
			</table>
			<div id="actions">
				<input type="submit" value="Continue" />
			</div>
		</form>
		<?php
	}
	foot();
}
// Settings and Admin account
elseif($step == 3 and !is_installed())
{
	head('install', $step);
	
	// Step 3.1: Check settings and account.
	$error = array();
	
	if(isset($_POST['settings']) and empty($_POST['settings']['title']))
	{
		$errors['title'] = 'Title cannot be blank.';
	}
	if(isset($_POST['settings']) and empty($_POST['admin']['username']))
	{
		$errors['username'] = 'Username cannot be blank.';
	}
	if(isset($_POST['settings']) and empty($_POST['admin']['password']))
	{
		$errors['password'] = 'Password cannot be blank.';
	}
	if(isset($_POST['settings']) and empty($_POST['admin']['email']))
	{
		$errors['email'] = 'Email cannot be blank.';
	}
	
	if(!count(@$errors) and isset($_POST['settings']))
	{
		?>
		<form action="index.php" method="post">
			<input type="hidden" name="step" value="<?php echo $step+1; ?>" />
			<input type="hidden" name="db" value='<?php echo $_POST['db']; ?>' />
			<input type="hidden" name="settings" value='<?php echo json_encode($_POST['settings']); ?>' />
			<input type="hidden" name="admin" value='<?php echo json_encode($_POST['admin']); ?>' />
			<h2>Confirm</h2>
			
			<h3>Traq Settings</h3>
			<table class="inputForm">
				<tr>
					<td class="label">Traq Title</td>
					<td><?php echo $_POST['settings']['title']?></td>
				</tr>
				<tr>
					<td class="label">Clean URI's</td>
					<td><?php echo @$_POST['settings']['seo_urls'] ? 'Yes' : 'No'; ?></td>
				</tr>
			</table>
			
			<h3>Admin Account</h3>
			<table class="inputForm">
				<tr>
					<td class="label">Username</td>
					<td><?php echo $_POST['admin']['username']?></td>
				</tr>
				<?php /*<tr>
					<td class="label">Password</td>
					<td><?php echo $_POST['admin']['password']?></td>
				</tr>*/ ?>
				<tr>
					<td class="label">Email</td>
					<td><?php echo $_POST['admin']['email']?></td>
				</tr>
			</table>
			
			<div id="actions">
				<input type="submit" value="Install" />
			</div>
		</form>
		<?php
	}
	// Show settings and account form.
	else
	{
		if(isset($_POST['settings']) and count($errors))
		{
		?>
		<div class="message error"><?php echo implode('<br />', $errors)?></div>
		<?php } ?>
		<form action="index.php" method="post">
			<input type="hidden" name="step" value="<?php echo $step; ?>" />
			<input type="hidden" name="db" value='<?php echo $_POST['db']; ?>' />
			<h2>Traq Settings</h2>
			<table class="inputForm">
				<tr>
					<td class="label">Traq Title</td>
					<td><input type="text" name="settings[title]" autocomplete="off" value="" /></td>
				</tr>
				<tr>
					<td class="label">Clean URI's</td>
					<td><input type="radio" value="1" id="cleanuri_yes" name="settings[seo_urls]" checked="checked" /><label for="cleanuri_yes">Yes</label> <input type="radio" value="0" id="cleanuri_no" name="settings[seo_urls]" /><label for="cleanuri_no">No</label></td>
				</tr>
			</table>
			
			<h2>Admin Account</h2>
			<table class="inputForm">
				<tr>
					<td class="label">Username</td>
					<td><input type="text" name="admin[username]" /></td>
				</tr>
				<tr>
					<td class="label">Password</td>
					<td><input type="password" name="admin[password]" /></td>
				</tr>
				<tr>
					<td class="label">Email</td>
					<td><input type="text" name="admin[email]" /></td>
				</tr>
			</table>
			
			<div id="actions">
				<input type="submit" value="Continue" />
			</div>
		</form>
		<?php
	}
	
	foot();
}
// Install
elseif($step == 4 and !is_installed())
{
	head('install');
	
	// Decode database confg, settings and admin account info
	$dbconf = json_decode($_POST['db'], true);
	$settings = json_decode($_POST['settings'], true);
	$admin = json_decode($_POST['admin'], true);
	
	$db = new Database($dbconf['server'], $dbconf['user'], $dbconf['pass'], $dbconf['dbname']);
	
	// Fetch the install SQL.
	$installsql = file_get_contents('install.sql');
	$installsql = str_replace('traq_', $dbconf['prefix'], $installsql);
	$queries = explode(';', $installsql);
	
	// Run the install queries.
	foreach($queries as $query) {
		if(!empty($query) && strlen($query) > 5) {
			$db->query($query);
		}
	}
	
	// Insert textile format text plugin
	$db->query("INSERT INTO `" . $dbconf['prefix'] . "plugins` (`name`, `author`, `website`, `version`, `enabled`, `install_sql`, `uninstall_sql`) VALUES
		('Textile Formatting', 'Jack', 'http://traqproject.org', '1.0', 1, '', '');");
	$db->query("INSERT INTO `" . $dbconf['prefix'] . "plugin_code` (`plugin_id`, `title`, `hook`, `code`, `execorder`, `enabled`) VALUES
		(" . $db->insertid() . ", 'formattext', 'function_formattext', '" . $db->res('global $textile;
if(!isset($textile)) {
	require(TRAQPATH."system/plugins/classTextile.php");
	$textile = new Textile;
}
$text = $textile->TextileThis($text);') . "', 0, 1);");
	
	// Insert Settings.
	$db->query("UPDATE ".$dbconf['prefix']."settings SET value='".$db->res($settings['title'])."' WHERE setting='title'");
	$db->query("UPDATE ".$dbconf['prefix']."settings SET value='".$db->res($settings['seo_urls'])."' WHERE setting='seo_urls'");
	
	// Create admin account
	$db->query("INSERT INTO ".$dbconf['prefix']."users (username, password, name, email, group_id, sesshash) VALUES
					('".$db->res($admin['username'])."', '".sha1($admin['password'])."', '".$db->res($admin['username'])."', '".$db->res($admin['email'])."', 1, '')");
	
	// Config file code
	$config_code = array();
	$config_code[] = '<?php';
	$config_code[] = '/**';
	$config_code[] = ' * Traq';
	$config_code[] = ' * Copyright (C) 2009-2011 Jack Polgar';
	$config_code[] = ' *';
	$config_code[] = ' * This file is part of Traq.';
	$config_code[] = ' * ';
	$config_code[] = ' * Traq is free software: you can redistribute it and/or modify';
	$config_code[] = ' * it under the terms of the GNU General Public License as published by';
	$config_code[] = ' * the Free Software Foundation; version 3 only.';
	$config_code[] = ' * ';
	$config_code[] = ' * Traq is distributed in the hope that it will be useful,';
	$config_code[] = ' * but WITHOUT ANY WARRANTY; without even the implied warranty of';
	$config_code[] = ' * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the';
	$config_code[] = ' * GNU General Public License for more details.';
	$config_code[] = ' * ';
	$config_code[] = ' * You should have received a copy of the GNU General Public License';
	$config_code[] = ' * along with Traq. If not, see <http://www.gnu.org/licenses/>.';
	$config_code[] = ' */';
	$config_code[] = '';
	$config_code[] = '$conf = array(';
	$config_code[] = "	'db' => array(";
	$config_code[] = "		'server' => '". $dbconf['server']."', // Database server";
	$config_code[] = "		'user' => '".$dbconf['user']."', // Database username";
	$config_code[] = "		'pass' => '".$dbconf['pass']."', // Database password";
	$config_code[] = "		'dbname' => '".$dbconf['dbname']."', // Database name";
	$config_code[] = "		'prefix' => '".$dbconf['prefix']."' // Table prefix";
	$config_code[] = "	),";
	$config_code[] = "	'general' => array(";
	$config_code[] = "		'authorized_only' => false, // Access for authorized users only";
	$config_code[] = "		'enable_notifications' => true // Enable email notifications.";
	$config_code[] = "	)";
	$config_code[] = ');';
	$config_code = implode(PHP_EOL, $config_code);
		
	if(!file_exists('../system/config.php') and is_writable('../system'))
	{
		$handle = fopen('../system/config.php', 'w+');
		fwrite($handle, $config_code);
		?><div align="center" class="message good">Installation Completed</div>
		<div id="actions"><a href="../admincp">AdminCP</a></div><?php
	}
	else
	{
		?>
		<div align="center" class="message error">The installer was unable to create the config file, please do this manually.</div><br />
		<div align="center">Save this code to <code>system/config.php</code> and the installation will be complete.</div>
		<br />
<pre id="config_code">
<?php echo htmlentities($config_code); ?>
</pre>
		<?php
	}
	foot();
}