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

// New/Edit Version
if(isset($_REQUEST['new']) or isset($_REQUEST['edit']))
{
	// Check for errors...
	if(isset($_POST['action']))
	{
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_version_name_blank');
		if(empty($_POST['project']))
			$errors['project'] = l('error_project_blank');	
	}
	
	// Create the version
	if(isset($_POST['action']) && $_POST['action'] == 'create')
		if(!count($errors))
		{
			$db->query("INSERT INTO ".DBPF."versions
			(version,project_id)
			VALUES(
			'".$db->res($_POST['name'])."',
			'".$db->res($_POST['project'])."'
			)");
			
			header("Location: versions.php");
		}
	
	// Save the version
	if(isset($_POST['action']) && $_POST['action'] == 'save')
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."versions SET
				version='".$db->res($_POST['name'])."',
				project_id='".$db->res($_POST['project'])."'
				WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
			
			header("Location: versions.php");
		}
	
	if(isset($_REQUEST['edit']))
		$version = $db->queryfirst("SELECT * FROM ".DBPF."versions WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
	else
		$version = $_POST;
	
	head(l((isset($_REQUEST['new']) ? 'new' : 'edit').'_version'),true,'projects');
	?>
	<?php if(isset($errors) && count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="versions.php?<?php echo (isset($_REQUEST['edit']) ? 'edit='.$_REQUEST['edit'] : 'new')?>" method="post">
	<input type="hidden" name="action" value="<?php echo (isset($_REQUEST['edit']) ? 'save' : 'create')?>" />
	<div class="thead"><?php echo l((isset($_REQUEST['new']) ? 'new' : 'edit').'_version')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('version_name_description')?></td>
				<td align="right"><input type="text" name="version" value="<?php echo $version['version']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('version_project_description')?></td>
				<td align="right">
					<select name="project">
					<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif($project['id'] == $version['project_id'],' selected="selected"')?>><?php echo $project['name']?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l((isset($_REQUEST['edit']) ? 'update' : 'create'))?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Delete Version
elseif(isset($_REQUEST['delete']))
{
	$db->query("DELETE FROM ".DBPF."versions WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	$db->query("UPDATE ".DBPF."tickets SET version_id='0' WHERE version_id='".$db->res($_REQUEST['delete'])."'");
	header("Location: versions.php?deleted");
}
// List Versions
else
{
	// Get Versions

	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects))
	{
		$info['versions'] = array();
		$fetchversions = $db->query("SELECT * FROM ".DBPF."versions WHERE project_id='".$info['id']."' ORDER BY version ASC");
		while($version = $db->fetcharray($fetchversions))
			$info['versions'][] = $version;
		
		$projects[] = $info;
	}
	
	head(l('versions'),true,'projects');
	?>
	<h2><?php echo l('versions')?></h2>
	
	<form action="versions.php?new" method="post">
	<input type="hidden" name="action" value="create" />
	<div class="thead"><?php echo l('new_version')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('version')?></th>
				<th width="200" align="left"><?php echo l('project')?></th>
				<th></th>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><input type="text" name="version" /></td>
				<td>
					<select name="project">
					<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"><?php echo $project['name']?></option>
					<?php } ?>
					</select>
				</td>
				<td align="right"><input type="submit" value="<?php echo l('create')?>" /></td>
			</tr>
		</table>
	</div>
	</form>
	<br />
	<?php
	foreach($projects as $project) { ?>
	<div class="thead"><?php echo $project['name']?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('version')?></th>
				<th></th>
			</tr>
			<?php foreach($project['versions'] as $version) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="versions.php?edit=<?php echo $version['id']?>"><?php echo $version['version']?></a></td>
				<td align="right">
					<a href="versions.php?edit=<?php echo $version['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$version['version'])?>')) { window.location = 'versions.php?delete=<?php echo $version['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($project['versions'])) { ?>
			<tr>
				<td align="center" colspan="3"><?php echo l('no_versions')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	}
	foot();
}
?>