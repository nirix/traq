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
	$fetchusers = $db->query("SELECT * FROM ".DBPREFIX."users ORDER BY username ASC");
	$users = array();
	while($info = $db->fetcharray($fetchusers)) {
		$users[] = $info;
	}
	
	adminheader('Users');
	?>
	<div id="content">
		<div class="content-group">
			<div class="content-title">Users</div>
			<table width="100%" class="componentlist" cellspacing="0" cellpadding="4">
				<thead>
					<tr>
						<th class="component">Username</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<? foreach($users as $user) { ?>
				<tr>
					<td class="component"><a href="users.php?action=modify&id=<?=$user['id']?>"><?=$user['username']?></a></td>
					<td class="actions"><a href="javascript:void(0);" onclick="if(confirm('Are you sure you want to delete user: <?=$user['username']?>')) { window.location='users.php?action=delete&id=<?=$user['id']?>' }">Delete</a></td>
				</tr>
				<? } ?>
			</table>
		</div>
	</div>
	<?
	adminfooter();
} elseif($_REQUEST['action'] == "modify") {
	if($_POST['do'] == "modify") {
		$errors = array();
		if($_POST['username'] == "") {
			$errors['username'] = "You must enter a Username";
		}
		if($db->numrows($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE username='".$db->escapestring($_POST['username'])."' AND id != '".$db->escapestring($_POST['userid'])."' LIMIT 1"))) {
			$errors['username'] = "Username is already in use";
		}
		if($db->numrows($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE username != '".$db->escapestring($_POST['username'])."' AND id='".$db->escapestring($_POST['userid'])."' LIMIT 1"))) {
			// Update Username in tables that store the username...
			
			// Attachments
			$db->query("UPDATE ".DBPREFIX."attachments SET ownername='".$db->escapestring($_POST['username'])."' WHERE ownerid='".$db->escapestring($_POST['userid'])."'");
			// Ticket Histroy
			$db->query("UPDATE ".DBPREFIX."tickethistory SET username='".$db->escapestring($_POST['username'])."' WHERE userid='".$db->escapestring($_POST['userid'])."'");
			// Tickets
			$db->query("UPDATE ".DBPREFIX."tickets SET ownername='".$db->escapestring($_POST['username'])."' WHERE ownerid='".$db->escapestring($_POST['userid'])."'");
			// Timeline
			$db->query("UPDATE ".DBPREFIX."timeline SET username='".$db->escapestring($_POST['username'])."' WHERE userid='".$db->escapestring($_POST['userid'])."'");
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		$db->query("UPDATE ".DBPREFIX."users SET username='".$db->escapestring($_POST['username'])."', email='".$db->escapestring($_POST['email'])."', groupid='".$db->escapestring($_POST['group'])."' WHERE id='".$db->escapestring($_POST['userid'])."' LIMIT 1");
		header("Location: users.php");
	} else {
		$user = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."users WHERE id='".$db->escapestring($_REQUEST['id'])."' LIMIT 1"));
		adminheader('Modify User');
		?>
		<div id="content">
			<form action="users.php?action=modify" method="post">
			<input type="hidden" name="do" value="modify" />
			<input type="hidden" name="userid" value="<?=$user['id']?>" />
			<div class="content-group">
				<div class="content-title">Modify User</div>
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
						<th>Username</th>
						<td><input type="text" name="username" value="<?=$user['username']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Email</th>
						<td><input type="text" name="email" value="<?=$user['email']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Group</th>
						<td>
							<select name="group" id="group">
							<? foreach(getgroups() as $group) { ?>
								<option value="<?=$group['id']?>"<?=($group['id'] == $user['groupid'] ? ' selected="selected"' : '')?>><?=$group['name']?></option> 
							<? } ?>
							</select>
						</td>
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
	$db->query("DELETE FROM ".DBPREFIX."users WHERE id='".$db->escapestring($_REQUEST['id'])."' LIMIT 1");
	header("Location: users.php");
}
?>