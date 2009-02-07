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

if($_REQUEST['action'] == "manage") {
	$fetchcomponents = $db->query("SELECT * FROM ".DBPREFIX."components ORDER BY project ASC");
	$components = array();
	while($info = $db->fetcharray($fetchcomponents)) {
		$info['projectinfo'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE id='".$info['project']."' LIMIT 1"));
		$components[] = $info;
	}
	
	adminheader('Components');
	?>
	<div id="content">
		<div class="content-group">
			<div class="content-title">Components</div>
			<table width="100%" class="componentlist" cellspacing="0" cellpadding="4">
				<thead>
					<th class="component">Component</th>
					<th class="project">Project</th>
					<th class="actions">Actions</th>
				</thead>
				<? foreach($components as $component) { ?>
				<tr>
					<td class="component"><a href="components.php?action=modify&component=<?=$component['id']?>"><?=$component['name']?></a></td>
					<td class="project"><?=$component['projectinfo']['name']?></td>
					<td class="actions">Delete</td>
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
			$errors['name'] = "You must enter a name";
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		$db->query("INSERT INTO ".DBPREFIX."components VALUES(0,
															'".$db->escapestring($_POST['name'])."',
															'".$db->escapestring($_POST['description'])."',
															'".$db->escapestring($_POST['project'])."'
															)");
		header("Location: components.php?action=manage");
	} else {
		adminheader('New Component');
		?>
		<div id="content">
			<form action="components.php?action=new" method="post">
			<input type="hidden" name="do" value="create" />
			<div class="content-group">
				<div class="content-title">New Component</div>
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
						<th>Description</th>
						<td><textarea name="description"></textarea></td>
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
						<td><button type="submit">Create Component</button></td>
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
			$errors['name'] = "You must enter a name";
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		$db->query("UPDATE ".DBPREFIX."components SET name='".$db->escapestring($_POST['name'])."', ".DBPREFIX."components.desc='".$db->escapestring($_POST['description'])."', project='".$db->escapestring($_POST['project'])."' WHERE id='".$db->escapestring($_POST['component'])."' LIMIT 1");
		header("Location: components.php?action=manage");
	} else {
		$component = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$db->escapestring($_REQUEST['component'])."' LIMIT 1"));
		adminheader('Modify Component');
		?>
		<div id="content">
			<form action="components.php?action=modify" method="post">
			<input type="hidden" name="do" value="modify" />
			<input type="hidden" name="component" value="<?=$component['id']?>" />
			<div class="content-group">
				<div class="content-title">Modify Component</div>
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
						<td><input type="text" name="name" value="<?=$component['name']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Description</th>
						<td><textarea name="description"><?=$component['desc']?></textarea></td>
					</tr>
					<tr valign="top">
						<th>Project</th>
						<td>
							<select name="project">
								<? foreach(getprojects() as $project) { ?>
								<option value="<?=$project['id']?>"<?=($component['project'] == $project['id'] ? ' selected="selected"' : '')?>><?=$project['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Update Component</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
}
?>