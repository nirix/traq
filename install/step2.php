<?php
/**
 * Traq 2
 * Copyright (c) 2009
 * All Rights Reserved
 *
 * $Id: index.php 2010-04-07 19:28:16Z pierre $
 */
	$error = false;
	if(file_exists('../system/config.php'))
	{
		require('../system/config.php');
		// Check connection
		$connect = @mysql_connect($conf['db']['server'],$conf['db']['user'],$conf['db']['pass']);
		if(!$connect)
		{
			$error = true;
			$checks['database']['name'] = 'Database Configuration';
			$checks['database']['class'] = 'bad';
			$checks['database']['message'] = 'Configuration exists, but cannot connect to database. Please check your connection settings';
			
		}
		else
		{
			// Check database
			if(!mysql_select_db($conf['db']['dbname']))
			{
				$error = true;
				$checks['database']['name'] = 'Database Configuration';
				$checks['database']['class'] = 'bad';
				$checks['database']['message'] = 'Database doesn\'t exist. Please check your configuration';
			}
			else
			{
				// Make sure Traq isn't already installed.
				$tables = mysql_query("SHOW TABLES");
				while($info = mysql_fetch_array($tables)) 
				{
					if($info['0'] == $conf['db']['prefix'].'settings')
					{
						$error_return = '<table width="400" align="center">
											<tr>
												<td align="center" class="bad"><h2>Error</h2>Traq is already installed. Please delete the installation folder</td>
											</tr>
										</table>';
					}
				}
			}
		}
	}
	
	// cache dir check
	$checks['cache_dir'] = array(
		'name' => "<code>cache/</code> directory",
		'class' => 'good',
		'message' => 'Writable'
	);
	if(!is_writable('../cache'))
	{
		$error = true;
		$checks['cache_dir']['class'] = 'bad';
		$checks['cache_dir']['message'] = 'Not writable';
	}

	// cache dir check
	$checks['system_dir'] = array(
		'name' => "<code>system/</code> directory",
		'class' => 'good',
		'message' => 'Writable'
	);
	if(!is_writable('../system'))
	{
		$error = true;
		$checks['system_dir']['class'] = 'bad';
		$checks['system_dir']['message'] = 'Not writable';
	}
	

	// php version
	$checks['php_version'] = array(
		'name' => "PHP version >= ".$min_php_ver,
		'class' => 'good',
		'message' => $tick.' ('.phpversion().')'
	);
	if(!version_compare(phpversion(), $min_php_ver, '>='))
	{
		$error = true;
		$checks['php_version']['class'] = 'bad';
		$checks['php_version']['message'] = $cross.' ('.phpversion().')';
	}

	// mysql version
	$checks['mysql_version'] = array(
		'name' => "MySQL version >= ".$min_mysql_ver,
		'class' => 'good',
		'message' => $tick.' ('.mysql_get_client_info().')'
	);
	if(!version_compare(mysql_get_client_info(), $min_mysql_ver, '>='))
	{
		$error = true;
		$checks['mysql_version']['class'] = 'bad';
		$checks['mysql_version']['message'] = $cross.' ('.mysql_get_client_info().')';
	}
	
	if(!isset($error_return) || empty($error_return))
	{
		?>
		<h4 align="center">System Configuration Checks</h4>
		<form action="index.php" method="post">
			<table width="100%" align="center" style="margin-left:30%" cellpadding="4" cellspacing="4">
			<? foreach($checks as $check) { ?>
				<tr>
					<td width="40%"><?=$check['name']?></td>
					<td class="<?=$check['class']?>" align=""><?=$check['message']?></td>
				</tr>
			<? } ?>
			</table>
			
			<? if(!$error) { ?>
				<div align="center"><input type="submit" value="Next" /></div>
				<input type="hidden" name="step" value="<?php echo $next_step; ?>" />
			<? } else { ?>
				<div align="center"><input type="submit" value="Refresh" />
					<br />
					<small>Fix the errors above and click on refresh</small>
				</div>
				<input type="hidden" name="step" value="<?php echo $step; ?>" />
			<? } ?>
		</form>
		<?
	} else {
		echo $error_return;
	}
	
?>
