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
	($hook = FishHook::hook('admin_versions_manage_start')) ? eval($hook) : false;
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPREFIX."projects ORDER BY name ASC");
	while($project = $db->fetcharray($fetchprojects)) {
		$project['versions'] = array();
		$fetchversions = $db->query("SELECT * FROM ".DBPREFIX."versions WHERE projectid='".$project['id']."' ORDER BY projectid ASC");
		while($info = $db->fetcharray($fetchversions)) {
			$info['projectinfo'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE id='".$info['projectid']."' LIMIT 1"));
			$project['versions'][] = $info;
			($hook = FishHook::hook('admin_versions_manage_fetchrows')) ? eval($hook) : false;
		}
		$projects[] = $project;
	}
	
	$fetchversions = $db->query("SELECT * FROM ".DBPREFIX."versions ORDER BY projectid ASC");
	$versions = array();
	while($info = $db->fetcharray($fetchversions)) {
		$info['projectinfo'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE id='".$info['projectid']."' LIMIT 1"));
		($hook = FishHook::hook('admin_versions_manage_update')) ? eval($hook) : false;
		$versions[] = $info;
	}
	
	adminheader('Versions');
	?>
	<div id="content">
		<div class="content-group">
			<div class="content-title">Versions</div>
			<table width="100%" class="componentlist" cellspacing="0" cellpadding="4">
				<thead>
					<tr>
						<th class="component">Version</th>
						<th class="project">Project</th>
						<th class="actions">Actions</th>
					</tr>
				</thead>
				<? foreach($projects as $project) { ?>
				<tr class="thead">
					<td colspan="3"><?=$project['name']?></td>
				</tr>
				<? foreach($project['versions'] as $version) { ?>
				<tr>
					<td class="component"><a href="versions.php?action=modify&version=<?=$version['id']?>"><?=$version['version']?></a></td>
					<td class="project"><?=$version['projectinfo']['name']?></td>
					<td class="actions"><a href="javascript:void(0);" onclick="if(confirm('Are you sure you want to delete version <?=$version['version']?> for project: <?=$version['projectinfo']['name']?>')) { window.location='versions.php?action=delete&version=<?=$version['id']?>' }">Delete</a></td>
				</tr>
				<? } ?>
				<? } ?>
			</table>
		</div>
	</div>
	<?
	adminfooter();
	($hook = FishHook::hook('admin_versions_manage_end')) ? eval($hook) : false;
} elseif($_REQUEST['action'] == "new") {
	($hook = FishHook::hook('admin_versions_new_start')) ? eval($hook) : false;
	if($_POST['do'] == "create") {
		$errors = array();
		if($_POST['version'] == "") {
			$errors['version'] = "You must enter a version";
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		$db->query("INSERT INTO ".DBPREFIX."versions VALUES(0,
															'".$db->escapestring($_POST['version'])."',
															'".$db->escapestring($_POST['project'])."'
															)");
		($hook = FishHook::hook('admin_versions_new_insert')) ? eval($hook) : false;
		header("Location: versions.php?action=manage");
	} else {
		adminheader('New Version');
		?>
		<div id="content">
			<form action="versions.php?action=new" method="post">
			<input type="hidden" name="do" value="create" />
			<div class="content-group">
				<div class="content-title">New Version</div>
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
						<th>Version</th>
						<td><input type="text" name="version" /></td>
					</tr>
					<tr valign="top">
						<th>Project</th>
						<td>
							<select name="project">
								<? foreach(getprojects() as $project) { ?>
								<option value="<?=$project['id']?>"><?=$project['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Create Version</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
	($hook = FishHook::hook('admin_versions_new_end')) ? eval($hook) : false;
} elseif($_REQUEST['action'] == "modify") {
	($hook = FishHook::hook('admin_versions_modify_start')) ? eval($hook) : false;
	if($_POST['do'] == "modify") {
		$errors = array();
		if($_POST['version'] == "") {
			$errors['version'] = "You must enter a version";
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		$db->query("UPDATE ".DBPREFIX."versions SET version='".$db->escapestring($_POST['version'])."', projectid='".$db->escapestring($_POST['project'])."' WHERE id='".$db->escapestring($_POST['versionid'])."' LIMIT 1");
		($hook = FishHook::hook('admin_versions_modify_update')) ? eval($hook) : false;
		header("Location: versions.php?action=manage");
	} else {
		$version = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$db->escapestring($_REQUEST['version'])."' LIMIT 1"));
		adminheader('Modify Version');
		?>
		<div id="content">
			<form action="versions.php?action=modify" method="post">
			<input type="hidden" name="do" value="modify" />
			<input type="hidden" name="versionid" value="<?=$version['id']?>" />
			<div class="content-group">
				<div class="content-title">Modify Version</div>
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
						<th>Version</th>
						<td><input type="text" name="version" value="<?=$version['version']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Project</th>
						<td>
							<select name="project">
								<? foreach(getprojects() as $project) { ?>
								<option value="<?=$project['id']?>"<?=($project['id'] == $version['projectid'] ? ' selected="selected"' : '')?>><?=$project['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Create Version</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
	($hook = FishHook::hook('admin_versions_modify_end')) ? eval($hook) : false;
} elseif($_REQUEST['action'] == "delete") {
	$db->query("DELETE FROM ".DBPREFIX."versions WHERE id='".$db->escapestring($_REQUEST['version'])."' LIMIT 1");
	($hook = FishHook::hook('admin_versions_delete')) ? eval($hook) : false;
	header("Location: versions.php");
}
?>