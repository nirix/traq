<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

require("global.php");

if(!$user->group->isadmin) {
	exit;
}

adminheader();
?>
<div id="content">
	<div class="content-group">
		<div class="content-title">Summary</div>
		<table width="500">
			<tr>
				<td>Projects</td>
				<td><?=$db->numrows($db->query("SELECT id FROM ".DBPREFIX."projects"))?></td>
			</tr>
			<tr>
				<td>Tickets</td>
				<td><?=$db->numrows($db->query("SELECT id FROM ".DBPREFIX."tickets"))?> total / <?=$db->numrows($db->query("SELECT id FROM ".DBPREFIX."tickets WHERE status >= 1"))?> open / <?=$db->numrows($db->query("SELECT id FROM ".DBPREFIX."tickets WHERE status <= 0"))?> closed</td>
			</tr>
			<tr>
				<td>Milestones</td>
				<td><?=$db->numrows($db->query("SELECT id FROM ".DBPREFIX."milestones"))?></td>
			</tr>
			<tr>
				<td>Versions</td>
				<td><?=$db->numrows($db->query("SELECT id FROM ".DBPREFIX."versions"))?></td>
			</tr>
			<tr>
				<td>Components</td>
				<td><?=$db->numrows($db->query("SELECT id FROM ".DBPREFIX."components"))?></td>
			</tr>
		</table>
	</div>
</div>
<?
adminfooter();
?>