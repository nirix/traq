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

head('Settings');
?>
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
				<td class="optiontitle" colspan="2"><?=l('seo_frieldly_urls')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('seo_friendly_urls_description')?></td>
				<td>
					
				</td>
			</tr>
	</table>
</div>
<br />
<div class="thead"><?=l('recaptcha')?></div>
<div class="tborder">
	<table width="100%" cellspacing="0">
		<tr>
			<td class="optiontitle first" colspan="2"><?=l('public_api_key')?></td>
		</tr>
		<tr class="<?=altbg()?>">
			<td><?=l('recaptcha_public_api_key_description')?></td>
			<td width="200"><input type="text" name="recaptcha_pub" value="<?=settings('recaptcha_pubkey')?>" /></td>
		</tr>
		<tr>
			<td class="optiontitle" colspan="2"><?=l('private_api_key')?></td>
		</tr>
		<tr class="<?=altbg()?>">
			<td><?=l('recaptcha_private_api_key_description')?></td>
			<td><input type="text" name="recaptcha_priv" value="<?=settings('recaptcha_privkey')?>" /></td>
		</tr>
	</table>
</div>
<?
foot();
?>