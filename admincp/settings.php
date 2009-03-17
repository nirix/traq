<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

require("global.php");

if(!$user->group->isadmin) {
	exit;
}

if($_POST['do'] == "update") {
	$db->query("UPDATE ".DBPREFIX."settings SET value='".$db->escapestring($_POST['title'])."' WHERE setting='title' LIMIT 1");
	$db->query("UPDATE ".DBPREFIX."settings SET value='".$db->escapestring($_POST['theme'])."' WHERE setting='theme' LIMIT 1");
	header("Location: settings.php?updated");
} else {
	$hiddenfiles = array('.','..','.svn');
	adminheader('Settings');
	?>
	<div id="content">
		<form action="settings.php" method="post">
		<input type="hidden" name="do" value="update" />
		<div class="content-group">
			<div class="content-title">Settings</div>
			<table width="400">
				<tr valign="top">
					<th>Site title</th>
					<td><input type="text" name="title" value="<?=$settings->title?>" /></td>
				</tr>
				<tr valign="top">
					<th>Theme</th>
					<td>
						<select name="theme">
							<?
							foreach(scandir('../templates') as $theme) {
								if(is_dir('../templates/'.$theme) && !in_array($theme,$hiddenfiles)) {
							?>
							<option value="<?=$theme?>"<?=($settings->theme == $theme ? ' selected="selected"' : '')?>><?=$theme?></option>
							<?
								}
							}
							?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<br />
		<div class="content-group">
			<div align="center" class="content-group-content"><button type="submit">Update</button></div>
		</div>
		</form>
	</div>
	<?
	adminfooter();
}
?>