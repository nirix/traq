<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Fetch required files
require('common.php');
require('../inc/version.php');
require('../inc/db.class.php');

// Intro
if(!isset($_POST['step']))
{
	head('install');
	?>
	<form action="install.php" method="post">
		<input type="hidden" name="step" value="1" />
		
		<table width="400" align="center">
			<tr>
				<td><code>config.php</code> file</td>
				<? if(file_exists('../inc/config.php')) { ?>
				<td class="good" align="right">Found</td>
				<? } else {
					$error = true;
				?>
				<td class="bad" align="right">Not found</td>
				<? } ?>
			</tr>
			<tr>
				<td>Database</td>
				<? if(mysql_connect($conf['db']['server'],$conf['db']['user'],$conf['db']['pass'])) { ?>
				<td class="good" align="right">Connected</td>
				<? } else {
					$error = true;
				?>
				<td class="bad" align="right">Error connecting</td>
				<? } ?>
			</tr>
			<tr>
				<td><code>cache</code> Directory</td>
				<? if(is_writable('../cache')) { ?>
				<td class="good" align="right">Writable</td>
				<? } else {
					$error = true;
				?>
				<td class="bad" align="right">Not writable</td>
				<? } ?>
			</tr>
		</table>
		
		<? if(!$error) { ?>
			<div align="center"><input type="submit" value="Install" /></div>
		<? } ?>
	</form>
	<?
	foot();
}
elseif($_POST['step'] == '1')
{
	head('install');
	
	
	
	foot();
}
?>