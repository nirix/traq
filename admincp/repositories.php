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

// Repository types
$repository_types = array();
($hook = FishHook::hook('admin_repositories')) ? eval($hook) : false;
$repository_types[] = array('file'=>'subversion.class.php','template'=>'subversion','class'=>'Subversion','name'=>'Subversion');

// New and Edit Repository
if(isset($_REQUEST['new']) or isset($_REQUEST['edit']))
{
	// Get the repository info
	if(isset($_REQUEST['edit']))
	{
		$repo = $db->queryfirst("SELECT * FROM ".DBPF."repositories WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
		$repo['info'] = (array)json_decode($repo['info']);
	}
	
	// Check for errors
	if(isset($_POST['action']))
	{
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_name_empty');
		if(isset($_REQUEST['edit']) && $db->numrows($db->query("SELECT name FROM ".DBPF."repositories WHERE name='".$db->res($_POST['name'])."' AND id!='".$repo['id']."' AND project_id='".$db->res($_POST['project_id'])."' LIMIT 1")))
			$errors['name'] = l('error_name_taken');
		if(isset($_REQUEST['new']) && $db->numrows($db->query("SELECT name FROM ".DBPF."repositories WHERE name='".$db->res($_POST['name'])."' AND project_id='".$db->res($_POST['project_id'])."' LIMIT 1")))
			$errors['name'] = l('error_name_taken');
		if(empty($_POST['location']))
			$errors['location'] = l('error_location_empty');
	}
	
	// Create the repository
	if($_POST['action'] == 'create')
		if(!count($errors))
		{
			$db->query("INSERT INTO ".DBPF."repositories
			(name,slug,location,info,main,project_id) VALUES(
			'".$db->res($_POST['name'])."',
			'".$db->res(slugit($_POST['name']))."',
			'".$db->res($_POST['location'])."',
			'".json_encode(array('file'=>$repository_types[$_POST['type']]['file'],'template'=>$repository_types[$_POST['type']]['template'],'class'=>$repository_types[$_POST['type']]['class']))."',
			'".($db->numrows($db->query("SELECT id FROM ".DBPF."repositories WHERE project_id='".$db->res($_POST['project_id'])."' LIMIT 1")) ? '0' : '1')."',
			'".$db->res($_POST['project_id'])."'
			)");
			header("Location: repositories.php?created");
		}
	
	// Save the repository information
	if($_POST['action'] == 'save')
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."repositories SET
			name='".$db->res($_POST['name'])."',
			slug='".$db->res(slugit($_POST['name']))."',
			info='".json_encode(array('file'=>$repository_types[$_POST['type']]['file'],'template'=>$repository_types[$_POST['type']]['template'],'class'=>$repository_types[$_POST['type']]['class']))."',
			location='".$db->res($_POST['location'])."',
			project_id='".$db->res($_POST['project_id'])."'
			WHERE id='".$repo['id']."' LIMIT 1");
			header("Location: repositories.php?saved");
		}
	
	head(l((isset($_REQUEST['edit']) ? 'edit' : 'new').'_repository'),true,'projects');
	?>
	<?php if(count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="repositories.php?<?php echo (isset($_REQUEST['edit']) ? 'edit='.$_REQUEST['edit'] : 'new')?>" method="post">
	<input type="hidden" name="action" value="<?php echo (isset($_REQUEST['edit']) ? 'save' : 'create')?>" />
	<div class="thead"><?php echo l((isset($_REQUEST['edit']) ? 'edit' : 'new').'_repository')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('repository_name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?php echo $repo['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('repository_project_description')?></td>
				<td width="200">
					<select name="project_id">
						<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif($project['id'] == $repo['project_id'],' selected="selected"')?>><?php echo $project['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('type')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('repository_type_description')?></td>
				<td width="200">
					<select name="type">
						<?php foreach($repository_types as $key => $type) { ?>
						<option value="<?php echo $key?>"<?php echo iif($type['file'] == $repo['info']['file'],' selected="selected"')?>><?php echo $type['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('location')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('repository_location_description')?></td>
				<td width="200">
					<input type="text" name="location" value="<?php echo $repo['location']?>" />
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l((isset($_REQUEST['edit']) ? 'update' : 'create'))?>" /></div>
	</div>
	<?php
	foot();
}
// Delete repository
elseif(isset($_REQUEST['delete']))
{
	$repo = $db->queryfirst("SELECT id,main,project_id FROM ".DBPF."repositories WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	$db->query("DELETE FROM ".DBPF."repositories WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	
	// Select a new main repo if we're deleting the current one.
	if($repo['main'])
		$db->query("UPDATE ".DBPF."repositories SET main='1' WHERE project_id='".$repo['project_id']."'");
		
	header("Location: repositories.php?deleted");
}
// List Repositories
else
{
	// Get Repositories
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects))
	{
		$info['repos'] = array();
		$fetchrepos = $db->query("SELECT * FROM ".DBPF."repositories WHERE project_id='".$info['id']."' ORDER BY name ASC");
		while($repository = $db->fetcharray($fetchrepos))
		{
			$info['repos'][] = $repository;
		}
		
		$projects[] = $info;
	}
	
	head(l('repositories'),true,'projects');
	?>
	<h2><?php echo l('repositories')?></h2>
	<?php
	foreach($projects as $project) { ?>
	<div class="thead"><?php echo $project['name']?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('repository')?></th>
				<th></th>
			</tr>
			<?php foreach($project['repos'] as $repo) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="repositories.php?edit=<?php echo $repo['id']?>"><?php echo $repo['name']?></a></td>
				<td align="right">
					<a href="repositories.php?edit=<?php echo $repo['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$repo['name'])?>')) { window.location = 'repositories.php?delete=<?php echo $repo['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($project['repos'])) { ?>
			<tr>
				<td align="center" colspan="3"><?php echo l('no_repositories')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	}
	foot();
}
?>