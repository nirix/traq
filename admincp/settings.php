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

if(isset($_REQUEST['save']))
{
	foreach($_POST as $field => $value)
	{
		$db->query("UPDATE ".DBPF."settings SET value='".$db->res($value)."' WHERE setting='".$db->res($field)."' LIMIT 1");
	}
	
	header("Location: settings.php?saved");
}

head('Settings');
?>
<form action="settings.php?save" method="post">
	<div class="thead"><?=l('general')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('traq_name')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('traq_name_description')?></td>
				<td width="200"><input type="text" name="title" value="<?=settings('title')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('single_project')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('single_project_description')?></td>
				<td><input type="text" name="single_project" value="<?=(!settings('single_project') ? '' : settings('single_project'))?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('seo_friendly_urls')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('seo_friendly_urls_description')?></td>
				<td>
					<input type="radio" name="seo_urls" value="1" id="seo_urls_yes"<?=(settings('seo_urls') ? ' checked="checked"' :'')?> /> <label for="seo_urls_yes">Yes</label>
					<input type="radio" name="seo_urls" value="0" id="seo_urls_no"<?=(!settings('seo_urls') ? ' checked="checked"' :'')?> /> <label for="seo_urls_no">No</label>
				</td>
			</tr>
		</table>
	</div>
	<br />
	<div class="thead"><?=l('users')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('allow_registration')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('allow_registration_description')?></td>
				<td width="200">
					<input type="radio" name="allow_registration" value="1" id="allow_registration_yes"<?=(settings('allow_registration') ? ' checked="checked"' :'')?> /> <label for="allow_registration_yes">Yes</label>
					<input type="radio" name="allow_registration" value="0" id="allow_registration_no"<?=(!settings('allow_registration') ? ' checked="checked"' :'')?> /> <label for="allow_registration_no">No</label>
				</td>
			</tr>
		</table>
	</div>
	<br />
	<div class="thead"><?=l('recaptcha')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('enable_recaptcha')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('enable_recaptcha_description')?></td>
				<td width="200">
					<input type="radio" name="recaptcha_enabled" value="1" id="recaptcha_enable_yes"<?=(settings('recaptcha_enabled') ? ' checked="checked"' :'')?> /> <label for="recaptcha_enable_yes">Yes</label>
					<input type="radio" name="recaptcha_enabled" value="0" id="recaptcha_enable_no"<?=(!settings('recaptcha_enabled') ? ' checked="checked"' :'')?> /> <label for="recaptcha_enable_no">No</label>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('recaptcha_public_api_key')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('recaptcha_public_api_key_description')?></td>
				<td width="200"><input type="text" name="recaptcha_pub" value="<?=settings('recaptcha_pubkey')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('recaptcha_private_api_key')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('recaptcha_private_api_key_description')?></td>
				<td><input type="text" name="recaptcha_priv" value="<?=settings('recaptcha_privkey')?>" /></td>
			</tr>
		</table>
	</div>
	<br />
	<div align="center"><input type="submit" value="<?=l('save_settings')?>" /></div>
</form>
<?
($hook = FishHook::hook('admin_settings')) ? eval($hook) : false;

foot();
?>