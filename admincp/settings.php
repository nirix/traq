<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

include("global.php");

authenticate();

if($_POST['action'] == 'save')
{
	foreach($_POST as $field => $value)
	{
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
				<td class="optiontitle" colspan="2"><?php echo l('single_project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('single_project_description')?></td>
				<td><input type="text" name="single_project" value="<?php echo (!settings('single_project') ? '' : settings('single_project'))?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('seo_friendly_urls')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('seo_friendly_urls_description')?></td>
				<td>
					<input type="radio" name="seo_urls" value="1" id="seo_urls_yes"<?php echo (settings('seo_urls') ? ' checked="checked"' :'')?> /> <label for="seo_urls_yes">Yes</label>
					<input type="radio" name="seo_urls" value="0" id="seo_urls_no"<?php echo (!settings('seo_urls') ? ' checked="checked"' :'')?> /> <label for="seo_urls_no">No</label>
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
				<td width="200"><input type="text" name="recaptcha_pub" value="<?php echo settings('recaptcha_pubkey')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('recaptcha_private_api_key')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('recaptcha_private_api_key_description')?></td>
				<td><input type="text" name="recaptcha_priv" value="<?php echo settings('recaptcha_privkey')?>" /></td>
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