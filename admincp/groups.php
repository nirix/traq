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

if($_REQUEST['action'] == "manage" || $_REQUEST['action'] == '') {
	adminheader('Usergroups');
	?>
	<div id="content">
		<div class="content-group">
			<div class="content-title">Usergroups</div>
			<table width="100%" class="componentlist" cellspacing="0" cellpadding="4">
				<thead>
					<th class="component">Name</th>
					<th class="actions">Actions</th>
				</thead>
				<? foreach(getgroups() as $group) { ?>
				<tr>
					<td class="component"><a href="groups.php?action=modify&id=<?=$group['id']?>"><?=$group['name']?></a></td>
					<td class="actions"><? if(!in_array($group['id'],array(1,2,3))) { ?><a href="javascript:void(0);" onclick="if(confirm('Are you sure you want to delete user: <?=$group['name']?>')) { window.location='groups.php?action=delete&id=<?=$group['id']?>' }">Delete</a><? } else {?><s>Delete</s><? } ?></td>
				</tr>
				<? } ?>
			</table>
		</div>
	</div>
	<?
	adminfooter();
} elseif($_REQUEST['action'] == "new") {
	if($_POST['do'] == "create") {
		$errors = array();
		if($_POST['name'] == "") {
			$errors['name'] = "You must enter a Name";
		}
		if($db->numrows($db->query("SELECT id,name FROM ".DBPREFIX."usergroups WHERE name='".$db->escapestring($_POST['name'])."' AND id != '".$db->escapestring($_POST['groupid'])."' LIMIT 1"))) {
			$errors['name'] = "Name is already in use";
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		if(!isset($_POST['isadmin'])) {
			$_POST['isadmin'] = 0;
		}
		if(!isset($_POST['updatetickets'])) {
			$_POST['updatetickets'] = 0;
		}
		$db->query("INSERT INTO ".DBPREFIX."usergroups VALUES(0,'".$db->escapestring($_POST['name'])."',".$db->escapestring($_POST['isadmin']).",".$db->escapestring($_POST['updatetickets']).")");
		header("Location: groups.php");
	} else {
		adminheader('New Usergroup');
		?>
		<div id="content">
			<form action="groups.php?action=new" method="post">
			<input type="hidden" name="do" value="create" />
			<div class="content-group">
				<div class="content-title">New Usergroup</div>
				<? if(count($errors)) { ?>
				<div class="content-group-content">
					<div class="errormessage">
					<? foreach($errors as $error) { ?>
					<?=$error?><br />
					<? } ?>
					</div>
				</div>
				<? } ?>
				<table width="400">
					<tr valign="top">
						<th>Name</th>
						<td><input type="text" name="name" /></td>
					</tr>
					<tr valign="top">
						<th>Access AdminCP</th>
						<td><input type="checkbox" name="isadmin" value="1" /></td>
					</tr>
					<tr valign="top">
						<th>Update Tickets</th>
						<td><input type="checkbox" name="updatetickets" value="1" /></td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Create</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
} elseif($_REQUEST['action'] == "modify") {
	if($_POST['do'] == "modify") {
		$errors = array();
		if($_POST['name'] == "") {
			$errors['name'] = "You must enter a Name";
		}
		if($db->numrows($db->query("SELECT id,name FROM ".DBPREFIX."usergroups WHERE name='".$db->escapestring($_POST['name'])."' AND id != '".$db->escapestring($_POST['groupid'])."' LIMIT 1"))) {
			$errors['name'] = "Name is already in use";
		}
		$_REQUEST['id'] = $_POST['groupid'];
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		if(!isset($_POST['isadmin'])) {
			$_POST['isadmin'] = 0;
		}
		if(!isset($_POST['updatetickets'])) {
			$_POST['updatetickets'] = 0;
		}
		$db->query("UPDATE ".DBPREFIX."usergroups SET name='".$db->escapestring($_POST['name'])."'".($_POST['groupid'] != 3 ? ", isadmin='".$db->escapestring($_POST['isadmin'])."', updatetickets='".$db->escapestring($_POST['updatetickets'])."'" : '')." WHERE id='".$db->escapestring($_POST['groupid'])."' LIMIT 1");
		header("Location: groups.php");
	} else {
		$group = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."usergroups WHERE id='".$db->escapestring($_REQUEST['id'])."' LIMIT 1"));
		adminheader('Modify Usergroup');
		?>
		<div id="content">
			<form action="groups.php?action=modify" method="post">
			<input type="hidden" name="do" value="modify" />
			<input type="hidden" name="groupid" value="<?=$group['id']?>" />
			<div class="content-group">
				<div class="content-title">Modify Usergroup</div>
				<? if(count($errors)) { ?>
				<div class="content-group-content">
					<div class="errormessage">
					<? foreach($errors as $error) { ?>
					<?=$error?><br />
					<? } ?>
					</div>
				</div>
				<? } ?>
				<table width="400">
					<tr valign="top">
						<th>Name</th>
						<td><input type="text" name="name" value="<?=$group['name']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Access AdminCP</th>
						<td><input type="checkbox" name="isadmin" value="1"<?=($group['isadmin'] ? ' checked="checked"' : '')?><?=($group['id'] == 3 ? ' disabled="disabled"' : '')?> /></td>
					</tr>
					<tr valign="top">
						<th>Update Tickets</th>
						<td><input type="checkbox" name="updatetickets" value="1"<?=($group['updatetickets'] ? ' checked="checked"' : '')?><?=($group['id'] == 3 ? ' disabled="disabled"' : '')?> /></td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Save</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
} elseif($_REQUEST['action'] == "delete") {
	if(!in_array($_REQUEST['id'],array(1,2,3))) {
		$db->query("DELETE FROM ".DBPREFIX."usergroups WHERE id='".$db->escapestring($_REQUEST['id'])."' LIMIT 1");
		header("Location: groups.php");
	}
}
?>