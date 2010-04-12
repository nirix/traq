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

// Get the statistics...
$projects = $db->numrows($db->query("SELECT id FROM ".DBPF."projects"));
$tickets = $db->numrows($db->query("SELECT id FROM ".DBPF."tickets"));
$milestones = $db->numrows($db->query("SELECT id FROM ".DBPF."milestones"));
$users = $db->numrows($db->query("SELECT id FROM ".DBPF."users"));

head(l('overview'));
?>
<?php if($update = check4update()) { ?>
		<div id="update_available">
			<?php echo l('update_available_x_download',$update->announcement,$update->name,$update->download)?>
		</div>
		<?php } ?>
<div class="thead"><?php echo l('statistics')?></div>
<div class="tborder">
	<table width="100%" cellspacing="0">
		<tr class="<?php echo altbg()?>">
			<td width="150"><?php echo l('projects')?></td>
			<td><?php echo $projects?></td>
			<td width="150"><?php echo l('tickets')?></td>
			<td><?php echo $tickets?></td>
		</tr>
		<tr class="<?php echo altbg()?>">
			<td width="150"><?php echo l('users')?></td>
			<td><?php echo $users?></td>
			<td width="150"><?php echo l('milestones')?></td>
			<td><?php echo $milestones?></td>
		</tr>
	</table>
</div>
<?php
foot();
?>