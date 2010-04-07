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

// Get the statistics...
$projects = $db->numrows($db->query("SELECT id FROM ".DBPF."projects"));
$tickets = $db->numrows($db->query("SELECT id FROM ".DBPF."tickets"));
$users = $db->numrows($db->query("SELECT id FROM ".DBPF."users"));

head(l('overview'));
?>
<div class="thead">Statistics</div>
<div class="tborder">
	<table width="100%" cellspacing="0">
		<tr class="<?php echo altbg()?>">
			<td width="150"><?php echo l('projects')?></td>
			<td><?php echo $projects?></td>
		</tr>
		<tr class="<?php echo altbg()?>">
			<td width="150"><?php echo l('tickets')?></td>
			<td><?php echo $tickets?></td>
		</tr>
		<tr class="<?php echo altbg()?>">
			<td width="150"><?php echo l('users')?></td>
			<td><?php echo $users?></td>
		</tr>
	</table>
</div>
<?php
foot();
?>