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

include("global.php");

authenticate();

// Save the settings
if(isset($_POST['action']) && $_POST['action'] == 'save')
{
	// Loop through the fields and save
	foreach($_POST as $field => $value)
	{
		($hook = FishHook::hook('admin_settings_save')) ? eval($hook) : false;
		$db->query("UPDATE ".DBPF."settings SET value='".$db->res($value)."' WHERE setting='".$db->res($field)."' LIMIT 1");
	}
	header("Location: settings.php?saved");
}

head('Settings');
?>
<form action="settings.php" method="post">
	<input type="hidden" name="action" value="save" />
	<div class="thead"><?php echo l('general')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('traq_name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('traq_name_description')?></td>
				<td width="200"><input type="text" name="title" value="<?php echo settings('title')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('seo_friendly_urls')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('seo_friendly_urls_description')?></td>
				<td>
					<input type="radio" name="seo_urls" value="1" id="seo_urls_yes"<?php echo (settings('seo_urls') ? ' checked="checked"' :'')?> /> <label for="seo_urls_yes"><?php echo l('yes')?></label>
					<input type="radio" name="seo_urls" value="0" id="seo_urls_no"<?php echo (!settings('seo_urls') ? ' checked="checked"' :'')?> /> <label for="seo_urls_no"><?php echo l('no')?></label>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('Theme')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('theme_description')?></td>
				<td>
					<select name="theme">
					<?php foreach(scandir('../system/views') as $theme) { if(in_array($theme,array('.','..','.svn','traq.templates')) or !is_dir('../system/views/'.$theme)) continue; ?>
						<option value="<?php echo $theme?>"<?php echo iif(settings('theme') == $theme,' selected="selected"')?>><?php echo str_replace('.php','',$theme)?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('Language')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('language_description')?></td>
				<td>
					<select name="locale">
					<?php foreach(get_locales() as $locale) { ?>
						<option value="<?php echo $locale['file']?>"<?php echo iif(settings('locale') == $locale['file'],' selected="selected"')?>><?php echo str_replace('.php','',$locale['name'])?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('check_for_update')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('check_for_update_description')?></td>
				<td>
					<input type="radio" name="check_for_update" value="1" id="check_for_update_yes"<?php echo (settings('check_for_update') ? ' checked="checked"' :'')?> /> <label for="check_for_update_yes"><?php echo l('yes')?></label>
					<input type="radio" name="check_for_update" value="0" id="check_for_update_no"<?php echo (!settings('check_for_update') ? ' checked="checked"' :'')?> /> <label for="check_for_update_no"><?php echo l('no')?></label>
				</td>
			</tr>
		</table>
	</div>
	<br />
	<div class="thead"><?php echo l('date_and_time')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('date_time_format')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('date_time_format_description')?></td>
				<td width="200"><input type="text" name="date_time_format" value="<?php echo settings("date_time_format")?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('timeline_day_and_time_format')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('timeline_day_format')?></td>
				<td width="200"><input type="text" name="timeline_day_format" value="<?php echo settings("timeline_day_format")?>" /></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('timeline_time_format')?></td>
				<td width="200"><input type="text" name="timeline_time_format" value="<?php echo settings("timeline_time_format")?>" /></td>
			</tr>
		</table>
	</div>
	<br />
	<div class="thead"><?php echo l('users')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('allow_registration')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('allow_registration_description')?></td>
				<td width="200">
					<input type="radio" name="allow_registration" value="1" id="allow_registration_yes"<?php echo (settings('allow_registration') ? ' checked="checked"' :'')?> /> <label for="allow_registration_yes">Yes</label>
					<input type="radio" name="allow_registration" value="0" id="allow_registration_no"<?php echo (!settings('allow_registration') ? ' checked="checked"' :'')?> /> <label for="allow_registration_no">No</label>
				</td>
			</tr>
		</table>
	</div>
	<br />
	<div class="thead"><?php echo l('recaptcha')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('enable_recaptcha')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('enable_recaptcha_description')?></td>
				<td width="200">
					<input type="radio" name="recaptcha_enabled" value="1" id="recaptcha_enable_yes"<?php echo (settings('recaptcha_enabled') ? ' checked="checked"' :'')?> /> <label for="recaptcha_enable_yes">Yes</label>
					<input type="radio" name="recaptcha_enabled" value="0" id="recaptcha_enable_no"<?php echo (!settings('recaptcha_enabled') ? ' checked="checked"' :'')?> /> <label for="recaptcha_enable_no">No</label>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('recaptcha_public_api_key')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('recaptcha_public_api_key_description')?></td>
				<td width="200"><input type="text" name="recaptcha_pubkey" value="<?php echo settings('recaptcha_pubkey')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('recaptcha_private_api_key')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('recaptcha_private_api_key_description')?></td>
				<td><input type="text" name="recaptcha_privkey" value="<?php echo settings('recaptcha_privkey')?>" /></td>
			</tr>
		</table>
	</div>
	<br />
	<div align="center"><input type="submit" value="<?php echo l('save_settings')?>" /></div>
</form>
<?php
($hook = FishHook::hook('admin_settings')) ? eval($hook) : false;

foot();
?>