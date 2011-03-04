<?php
/**
 * Traq 2
 * Copyright (c) 2009
 * All Rights Reserved
 *
 * $Id: index.php 2010-04-07 19:28:16Z pierre $
 */

$error = $return = false;

$host = '';
$user = '';
$pass = '';
$db = '';
$prefix = (isset($_POST['prefix']) && !empty($_POST['prefix'])) ? $_POST['prefix'] : 'traq_';

if(isset($_POST['checkdb']) && $_POST['checkdb'] == 'true')
{
	$host = $_POST['host'];
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	$db = $_POST['db'];
	$prefix = $_POST['prefix'];
	
	if(empty($host) || empty($user) || empty($db))
	{
		$error = true;
		$msg = 'Please enter the following fields: <ul align="left">';
		
			if(empty($host))
			{
				$msg .= '<li>MySQL Host</li>';
			}

			if(empty($user))
			{
				$msg .= '<li>MySQL User</li>';
			}
			
			if(empty($db))
			{
				$msg .= '<li>Database Name</li>';
			}
		$msg .= '</ul>';
	} else if(!@mysql_connect($host, $user, $pass))
	{
		$error = true;
		
		$msg = "Unable to connect to database - ".mysql_error();
	} else if(!@mysql_select_db($db))
	{
		$error = true;
		
		$msg = "Unable to select database - ".mysql_error();
	}
	
	if(!$error)
	{
		//Write configuration
		$fp = fopen('../system/config.php', 'w+');
		
		$txt = "<?php
/**
 * Traq 2
 * Copyright (c) 2009, 2010 Jack Polgar
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
 * \$Id$
 */

\$conf = array();

\$conf['db']['server'] = '".$host."'; // Database Server
\$conf['db']['user'] = '".$user."'; // Database Username
\$conf['db']['pass'] = '".$pass."'; // Database Password
\$conf['db']['dbname'] = '".$db."'; // Database Name
\$conf['db']['prefix'] = '".$prefix."'; // Table prefix

\$conf['general']['authorized_only'] = false; // access for authorized users only";
		if(!fwrite($fp, $txt))
		{
			$error = true;
			$msg = 'Unable to write the configuration file';
		} else {
			$msg = 'Configuration file written<br />';
		}
		fclose($fp);
		
		if(!$error)
		{
			include('../system/config.php');
			include('../system/libraries/db.class.php');
			
			// Connect to the Database.
			mysql_connect($conf['db']['server'],$conf['db']['user'],$conf['db']['pass']);
			mysql_select_db($conf['db']['dbname']);
			
			$sql = false;
			$tables = mysql_query("SHOW TABLES");
			while($info = mysql_fetch_array($tables)) 
			{
				if($info['0'] == $conf['db']['prefix'].'settings')
				{
					$msg .= 'Database already installed';
					$sql = true;
				}
			}
			
			if(!$sql)
			{
				// Fetch the install SQL.
				$installsql = file_get_contents('install.sql');
				$installsql = str_replace('traq_',$conf['db']['prefix'],$installsql);
				$queries = explode(';',$installsql);
				
				// Run the install queries.
				foreach($queries as $query) 
				{
					if(!empty($query)) {
						if(!@mysql_query($query))
						{
							$error = true;
							$msg = mysql_error();
						}
					}
				}
			}
		}

		if(!$error)
		{
			$return = true;
			if(!$sql)
			{
				$msg .= 'Database installed successfully';
			}
		}
	}
}

echo '<h4 align="center">Database Configuration</h4>';

if($return)
{
	echo '<form action="index.php" method="post">
			<table width="100%" align="center" style="margin-left:20%" cellpadding="4" cellspacing="4">
				<tr>
					<td align="center" class="good">
						'.$msg.'
					</td>
				</tr>
				<tr>
					<td align="center" class="good">
						<input type="submit" value="Next" />
						 <input type="hidden" name="step" value="'.$next_step.'" />
					</td>
				</tr>
			</table>
		</form>';
} else {
	if($error)
	{
		echo '<table width="100%" align="center">
					<tr>
						<td align="center" class="bad">
							
							'.error($msg).'
						</td>
					</tr>
				</table>';
	}
	?>
	<form action="index.php" method="post">

		<table width="400" align="center">
			<tr>
				<td>Mysql Host</td>
				<td class="" align="right"><input type="text" name="host" value="<?php echo $host; ?>" /></td>
				<td><small>(e.g localhost)</small></td>
			</tr>
			<tr>
				<td>Mysql User</td>
				<td class="" align="right"><input type="text" name="user" value="<?php echo $user; ?>" /></td>
			</tr>
			<tr>
				<td>Mysql Pass</td>
				<td class="" align="right"><input type="password" name="pass" value="<?php echo $pass; ?>" /></td>
			</tr>
			<tr>
				<td>Database Name</td>
				<td class="" align="right"><input type="text" name="db" value="<?php echo $db; ?>" /></td>
			</tr>
			<tr>
				<td>Table Prefix</td>
				<td class="" align="right"><input type="text" name="prefix"  value="<?php echo $prefix; ?>" /></td>
			</tr>
		</table>
			
		<div align="center">
			<input type="submit" value="Install" />
		</div>
		<input type="hidden" name="step" value="<?php echo $step; ?>" />
		<input type="hidden" name="checkdb" value="true" />
			
	</form>
<?php
}
?>
