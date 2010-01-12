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

$projects = $db->numrows($db->query("SELECT id FROM ".DBPF."projects"));
$tickets = $db->numrows($db->query("SELECT id FROM ".DBPF."tickets"));
$users = $db->numrows($db->query("SELECT id FROM ".DBPF."users"));

head(l('overview'));
?>
<div class="thead">Statistics</div>
<div class="tborder">
	<table width="100%" cellspacing="0">
		<tr class="<?=altbg()?>">
			<td width="150"><?=l('projects')?></td>
			<td><?=$projects?></td>
		</tr>
		<tr class="<?=altbg()?>">
			<td width="150"><?=l('tickets')?></td>
			<td><?=$tickets?></td>
		</tr>
		<tr class="<?=altbg()?>">
			<td width="150"><?=l('users')?></td>
			<td><?=$users?></td>
		</tr>
	</table>
</div>
<?
foot();
?>