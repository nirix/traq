<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Set the full path to the Traq folder
define('TRAQPATH',str_replace(pathinfo('../index.php',PATHINFO_BASENAME),'','../index.php'));

include("global.php");

authenticate();

head('Settings');
?>
<div class="tborder">
	<div class="thead">General</div>
	<table width="100%" cellspacing="0">
		<tr>
			<td class="optiontitle" colspan="2">Traq Name</td>
		</tr>
		<tr class="<?=altbg()?>">
			<td>The name of the Traq installation.</td>
			<td width="200"><input type="text" name="title" value="<?=settings('title')?>" /></td>
		</tr>
		<tr>
			<td class="optiontitle" colspan="2">Single Project</td>
		</tr>
		<tr class="<?=altbg()?>">
			<td>To use Traq as a single project tracker, enter it's "slug".</td>
			<td><input type="text" name="single_project" value="<?=(!settings('single_project') ? '' : settings('single_project'))?>" /></td>
		</tr>
			<tr>
				<td class="optiontitle" colspan="2">mod_rewrite</td>
			</tr>
			<tr class="<?=altbg()?>">
				<td>Choose the respective option if you have mod_rewrite enabled.</td>
				<td>
					
				</td>
			</tr>
	</table>
</div>
<br />
<div class="tborder">
	<div class="thead">reCaptcha</div>
	<table width="100%" cellspacing="0">
		<tr>
			<td class="optiontitle" colspan="2">Public Key</td>
		</tr>
		<tr class="<?=altbg()?>">
			<td>Public API Key.</td>
			<td width="200"><input type="text" name="recaptcha_pub" value="<?=settings('recaptcha_pubkey')?>" /></td>
		</tr>
		<tr>
			<td class="optiontitle" colspan="2">Private Key</td>
		</tr>
		<tr class="<?=altbg()?>">
			<td>Private API Key.</td>
			<td><input type="text" name="recaptcha_priv" value="<?=settings('recaptcha_privkey')?>" /></td>
		</tr>
	</table>
</div>
<?
foot();
?>