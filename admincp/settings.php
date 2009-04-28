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
	$db->query("UPDATE ".DBPREFIX."settings SET value='".$db->escapestring($_POST['cleanuris'])."' WHERE setting='uritype' LIMIT 1");
	$db->query("UPDATE ".DBPREFIX."settings SET value='".$db->escapestring($_POST['akismetkey'])."' WHERE setting='akismetkey' LIMIT 1");
	($hook = FishHook::hook('admin_settings_save')) ? eval($hook) : false;
	header("Location: settings.php?updated");
} else {
	($hook = FishHook::hook('admin_settings_start')) ? eval($hook) : false;
	$hiddenfiles = array('.','..','.svn');
	adminheader('Settings');
	?>
	<div id="content">
		<form action="settings.php" method="post">
		<input type="hidden" name="do" value="update" />
		<div class="content-group">
			<div class="content-title">Settings</div>
			<table width="100%">
				<tr valign="top">
					<th width="150">Site title</th>
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
				<tr valign="top">
					<th>Clean URI's</th>
					<td>
						<label><input type="radio" name="cleanuris" value="1"<?=($settings->uritype == 1 ? ' checked="checked"' : '')?> />mod_rewrite: <?=$_SERVER['HTTP_HOST']?>/project/ticket/4/</label><br />
						<label><input type="radio" name="cleanuris" value="2"<?=($settings->uritype == 2 ? ' checked="checked"' : '')?> />no mod_rewrite: <?=$_SERVER['HTTP_HOST']?>/index.php/project/ticket/4/</label>
					</td>
				</tr>
			</table>
		</div>
		
		<? ($hook = FishHook::hook('admin_settings_table')) ? eval($hook) : false; ?>
		<br />
		<div class="content-group">
			<div class="content-title">Akismet Settings</div>
			<table width="100%">
				<tr valign="top">
					<th width="170">WordPress API Key</th>
					<td><input type="text" name="akismetkey" value="<?=$settings->akismetkey?>" /></td>
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
	($hook = FishHook::hook('admin_settings_end')) ? eval($hook) : false;
}
?>