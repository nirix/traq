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
	($hook = FishHook::hook('admin_project_manage_start')) ? eval($hook) : false;
	$fetchprojects = $db->query("SELECT * FROM ".DBPREFIX."projects ORDER BY name ASC");
	$projects = array();
	while($info = $db->fetcharray($fetchprojects)) {
		$projects[] = $info;
		($hook = FishHook::hook('admin_project_manage_fetchrows')) ? eval($hook) : false;
	}
	unset($fetchprojects,$info);
	adminheader('Projects');
	?>
	<div id="content">
		<div class="content-group">
			<div class="content-title">Projects</div>
			<table width="100%" class="projectlist" cellspacing="0" cellpadding="4">
				<thead>
					<tr>
						<th class="project">Project</th>
						<th class="slug">Slug</th>
						<th class="managers">Managers</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<? foreach($projects as $project) { ?>
				<tr>
					<td class="project"><a href="projects.php?action=modify&project=<?=$project['slug']?>"><?=$project['name']?></a></td>
					<td class="slug"><?=$project['slug']?></td>
					<td class="managers"><?=count(explode(',',$project['managers']))?></td>
					<td class="actions"><a href="javascript:void(0);" onclick="if(confirm('Are you sure you want to delete project: <?=$project['name']?>')) { window.location='projects.php?action=delete&project=<?=$project['slug']?>' }">Delete</a></td>
				</tr>
				<? } ?>
			</table>
		</div>
	</div>
	<?
	adminfooter();
	($hook = FishHook::hook('admin_project_manage_end')) ? eval($hook) : false;
} elseif($_REQUEST['action'] == "new") {
	($hook = FishHook::hook('admin_project_new_start')) ? eval($hook) : false;
	if($_POST['do'] == "create") {
		$errors = array();
		if($_POST['name'] == "") {
			$errors['name'] = "You must enter a name";
		}
		if($_POST['slug'] == "") {
			$errors['slug'] = "Slug cannot be blank";
		}
		if($db->numrows($db->query("SELECT slug FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($_POST['slug'])."' LIMIT 1"))) {
			$errors['slug'] = "Slug must be unique";
		}
		if(!count($_POST['managers'])) {
			$errors['managers'] = "You must select at least one manager";
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		$db->query("INSERT INTO ".DBPREFIX."projects VALUES(0,
															'".$db->escapestring($_POST['name'])."',
															'".$db->escapestring($_POST['slug'])."',
															'".$db->escapestring($_POST['description'])."',
															'".$db->escapestring(implode(',',$_POST['managers']))."',
															0,
															'".$db->escapestring($_POST['sourcelocation'])."'
															)");
		($hook = FishHook::hook('admin_project_new_insert')) ? eval($hook) : false;
		header("Location: projects.php?action=manage");
	} else {
		adminheader('New Project');
		?>
		<div id="content">
			<form action="projects.php?action=new" method="post">
			<input type="hidden" name="do" value="create" />
			<div class="content-group">
				<div class="content-title">New Project</div>
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
						<th>Slug</th>
						<td><input type="text" name="slug" /></td>
					</tr>
					<tr valign="top">
						<th>Description</th>
						<td><textarea name="description" rows="10" cols="50"></textarea></td>
					</tr>
					<tr valign="top">
						<th>Managers</th>
						<td>
							<select name="managers[]" multiple>
								<? foreach(getusers() as $user) { ?>
								<option value="<?=$user['id']?>"><?=$user['username']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th>SVN Location</th>
						<td><input type="text" name="sourcelocation" /></td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Create Project</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
	($hook = FishHook::hook('admin_project_new_end')) ? eval($hook) : false;
} elseif($_REQUEST['action'] == "modify") {
	($hook = FishHook::hook('admin_project_modify_start')) ? eval($hook) : false;
	if($_POST['do'] == "modify") {
		$errors = array();
		if($_POST['name'] == "") {
			$errors['name'] = "You must enter a name";
		}
		if($_POST['slug'] == "") {
			$errors['slug'] = "Slug cannot be blank";
		}
		if($_POST['slug'] != $_POST['project']) {
			if($db->numrows($db->query("SELECT slug FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($_POST['slug'])."' LIMIT 1"))) {
				$errors['slug'] = "Slug must be unique";
			}
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		$db->query("UPDATE ".DBPREFIX."projects SET name='".$db->escapestring($_POST['name'])."', slug='".$db->escapestring($_POST['slug'])."', ".DBPREFIX."projects.desc='".$db->escapestring($_POST['description'])."', managers='".$db->escapestring(implode(',',$_POST['managers']))."', sourcelocation='".$db->escapestring($_POST['sourcelocation'])."' WHERE slug='".$db->escapestring($_POST['project'])."' LIMIT 1");
		($hook = FishHook::hook('admin_project_modify_update')) ? eval($hook) : false;
		header("Location: projects.php?action=manage");
	} else {
		$project = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($_REQUEST['project'])."' LIMIT 1"));
		$project['managers'] = explode(',',$project['managers']);
		adminheader('Modify Project');
		?>
		<div id="content">
			<form action="projects.php?action=modify" method="post">
			<input type="hidden" name="do" value="modify" />
			<input type="hidden" name="project" value="<?=$project['slug']?>" />
			<div class="content-group">
				<div class="content-title">Modify Project</div>
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
						<td><input type="text" name="name" value="<?=$project['name']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Slug</th>
						<td><input type="text" name="slug" value="<?=$project['slug']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Description</th>
						<td><textarea name="description" rows="10" cols="50"><?=stripslashes($project['desc'])?></textarea></td>
					</tr>
					<tr valign="top">
						<th>Managers</th>
						<td>
							<select name="managers[]" multiple>
								<? foreach(getusers() as $user) { ?>
								<option value="<?=$user['id']?>"<?=(in_array($user['id'],$project['managers']) ? ' selected="selected"' : '')?>><?=$user['username']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th>SVN Location</th>
						<td><input type="text" name="sourcelocation" value="<?=$project['sourcelocation']?>" /></td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Update Project</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
	($hook = FishHook::hook('admin_project_modify_end')) ? eval($hook) : false;
} elseif($_REQUEST['action'] == "delete") {
	$project = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($_REQUEST['project'])."' LIMIT 1"));
	$db->query("DELETE FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($_REQUEST['project'])."' LIMIT 1");
	$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE projectid='".$project['id']."'");
	while($ticket = $db->fetcharray($fetchtickets)) {
		$db->query("DELETE FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."'");
		$db->query("DELETE FROM ".DBPREFIX."ticketcomments WHERE ticketid='".$ticket['id']."'");
	}
	$db->query("DELETE FROM ".DBPREFIX."tickets WHERE projectid='".$project['id']."'");
	$db->query("DELETE FROM ".DBPREFIX."milestones WHERE project='".$project['id']."'");
	$db->query("DELETE FROM ".DBPREFIX."timeline WHERE projectid='".$project['id']."'");
	$db->query("DELETE FROM ".DBPREFIX."versions WHERE projectid='".$project['id']."'");
	$db->query("DELETE FROM ".DBPREFIX."components WHERE project='".$project['id']."'");
	$db->query("DELETE FROM ".DBPREFIX."attachments WHERE projectid='".$project['id']."'");
	($hook = FishHook::hook('admin_project_delete')) ? eval($hook) : false;
	header("Location: projects.php");
}
?>