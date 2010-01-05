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

head(l('overview'));
?>
<div class="thead">Statistics</div>
<div class="tborder">
	<table width="100%" cellspacing="0">
		<tr class="<?=altbg()?>">
			<td width="150"><?=l('projects')?></td>
			<td></td>
		</tr>
		<tr class="<?=altbg()?>">
			<td width="150"><?=l('tickets')?></td>
			<td></td>
		</tr>
		<tr class="<?=altbg()?>">
			<td width="150"><?=l('users')?></td>
			<td></td>
		</tr>
	</table>
</div>
<?
foot();
?>