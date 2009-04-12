<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

require("global.php");

if(!$user->group->isadmin) {
	exit;
}

if($_REQUEST['action'] == "manage" || $_REQUEST['action'] == '') {
	($hook = FishHook::hook('admin_types_manage_start')) ? eval($hook) : false;
	adminheader('Types');
	?>
	<form action="types.php" method="post">
	<input type="hidden" name="action" value="save" />
	<div id="content">
		<div class="content-group">
			<div class="content-title">Types</div>
			<table width="100%" class="statustypeslist" cellspacing="0" cellpadding="4">
				<thead>
					<tr>
						<th class="id" width="50">ID</th>
						<th class="name">Name</th>
						<th class="actions" width="150"><div align="right">Actions</div></th>
					</tr>
				</thead>
				<tbody>
				<? foreach(gettypes() as $type) { ?>
				<tr>
					<td class="id"><input type="text" name="types[<?=$type['id']?>][id]" value="<?=$type['id']?>" style="width:50px;" /></td>
					<td class="name"><input type="text" name="types[<?=$type['id']?>][name]" value="<?=$type['name']?>" /></td>
					<td class="actions" align="right"><a href="javascript:void(0);" onclick="if(confirm('Are you sure you want to delete type: <?=$type['name']?>')) { window.location='types.php?action=delete&id=<?=$type['id']?>' }">Delete</a></td>
				</tr>
				<? } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" align="center"><input type="submit" value="Save" /> <input type="reset" value="Reset" /></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	</form>
	<br />
	<form action="types.php" method="post">
	<input type="hidden" name="action" value="create" />
	<div id="content">
		<div class="content-group">
			<div class="content-title">New Type</div>
			<table width="100%" cellspacing="0" cellpadding="4">
				<thead>
					<tr>
						<th width="50">ID</th>
						<th>Name</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<tr>
					<td><input type="text" name="id" style="width:50px;" /></td>
					<td><input type="text" name="name" /></td>
					<td colspan="3" align="right"><input type="submit" value="Create" /></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	</form>
	<?
	adminfooter();
	($hook = FishHook::hook('admin_types_manage_end')) ? eval($hook) : false;
} elseif($_POST['action'] == "save") {
	foreach($_POST['types'] as $id => $type) {
		if(!$db->numrows($db->query("SELECT * FROM ".DBPREFIX."types WHERE id='".$db->escapestring($type['id'])."' AND id != '".$db->escapestring($id)."' LIMIT 1"))) {
			$db->query("UPDATE ".DBPREFIX."types SET id='".$db->escapestring($type['id'])."', name='".$db->escapestring($type['name'])."' WHERE id='".$db->escapestring($id)."' LIMIT 1");
			($hook = FishHook::hook('admin_types_update')) ? eval($hook) : false;
		}
	}
	header("Location: types.php");
} elseif($_POST['action'] == "create") {
	if($db->numrows($db->query("SELECT * FROM ".DBPREFIX."types WHERE id != '".$db->escapestring($_POST['id'])."' LIMIT 1"))) {
		$db->query("INSERT INTO ".DBPREFIX."types VALUES(".$db->escapestring($_POST['id']).",'".$db->escapestring($_POST['name'])."')");
		($hook = FishHook::hook('admin_types_insert')) ? eval($hook) : false;
	}
	header("Location: types.php");
} elseif($_REQUEST['action'] == "delete") {
	$db->query("DELETE FROM ".DBPREFIX."types WHERE id='".$db->escapestring($_REQUEST['id'])."' LIMIT 1");
	($hook = FishHook::hook('admin_types_delete')) ? eval($hook) : false;
	header("Location: types.php");
}
?>