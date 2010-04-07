<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

include("global.php");

authenticate();

// New Component
if(isset($_REQUEST['new']))
{
	// Create the component
	if(isset($_POST['name']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_component_name_blank');
		if(empty($_POST['project']))
			$errors['project'] = l('error_project_blank');
		
		// If not errors, insert component.
		if(!count($errors))
		{
			$db->query("INSERT INTO ".DBPF."components
			(name,project_id)
			VALUES(
			'".$db->res($_POST['name'])."',
			'".$db->res($_POST['project'])."'
			)");
			
			header("Location: components.php");
		}
	}
	
	head(l('new_component'),true,'projects');
	?>
	<? if(count($errors)) { ?>
	<div class="message error">
		<? foreach($errors as $error) { ?>
		<?=$error?><br />
		<? } ?>
	</div>
	<? } ?>
	<form action="components.php?new" method="post">
	<div class="thead"><?=l('new_component')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('name')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('component_name_description')?></td>
				<td align="right"><input type="text" name="name" value="<?=$_POST['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('component_project_description')?></td>
				<td align="right">
					<select name="project">
					<? foreach(getprojects() as $project) { ?>
						<option value="<?=$project['id']?>"<?=iif($project['id'] == $_POST['project'],' selected="selected"')?>><?=$project['name']?></option>
					<? } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?=l('create')?>" /></div>
	</div>
	</form>
	<?
	foot();
}
// Edit Component
elseif(isset($_REQUEST['edit']))
{
	$component = $db->queryfirst("SELECT * FROM ".DBPF."components WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
	
	// Save the component
	if(isset($_POST['name']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_component_name_blank');
		if(empty($_POST['project']))
			$errors['project'] = l('error_project_blank');
		
		// If no errors, update component.
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."components SET
				name='".$db->res($_POST['name'])."',
				project_id='".$db->res($_POST['project'])."'
				WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
			
			header("Location: components.php");
		}
	}
	
	head(l('edit_component'),true,'projects');
	?>
	<? if(count($errors)) { ?>
	<div class="message error">
		<? foreach($errors as $error) { ?>
		<?=$error?><br />
		<? } ?>
	</div>
	<? } ?>
	<form action="components.php?edit=<?=$_REQUEST['edit']?>" method="post">
	<div class="thead"><?=l('edit_component')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('name')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('component_name_description')?></td>
				<td align="right"><input type="text" name="name" value="<?=$component['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('component_project_description')?></td>
				<td align="right">
					<select name="project">
					<? foreach(getprojects() as $project) { ?>
						<option value="<?=$project['id']?>"<?=iif($project['id'] == $component['project'],' selected="selected"')?>><?=$project['name']?></option>
					<? } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?=l('update')?>" /></div>
	</div>
	</form>
	<?
	foot();
}
// Delete Component
elseif(isset($_REQUEST['delete']))
{
	$db->query("DELETE FROM ".DBPF."components WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	$db->query("UPDATE ".DBPF."tickets SET component_id='0' WHERE component_id='".$db->res($_REQUEST['delete'])."'");
	header("Location: components.php?deleted");
}
// List Components
else
{
	// Get Components
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects))
	{
		$info['components'] = array();
		$fetchcomponents = $db->query("SELECT * FROM ".DBPF."components WHERE project_id='".$info['id']."' ORDER BY name ASC");
		while($component = $db->fetcharray($fetchcomponents))
		{
			$info['components'][] = $component;
		}
		
		$projects[] = $info;
	}
	
	head(l('components'),true,'projects');
	?>
	<h2><?=l('components')?></h2>
	<?
	
	foreach($projects as $project) { ?>
	<div class="thead"><?=$project['name']?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?=l('component')?></th>
				<th></th>
			</tr>
			<? foreach($project['components'] as $component) { ?>
			<tr class="<?=altbg()?>">
				<td><a href="components.php?edit=<?=$component['id']?>"><?=$component['name']?></a></td>
				<td align="right">
					<a href="components.php?edit=<?=$component['id']?>"><img src="images/pencil.png" alt="<?=l('edit')?>" title="<?=l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?=l('confirm_delete_component_x',$component['name'])?>')) { window.location = 'components.php?delete=<?=$component['id']?>'; } return false;"><img src="images/delete.png" alt="<?=l('delete')?>" title="<?=l('delete')?>" /></a>
				</td>
			</tr>
			<? } ?>
			<? if(!count($project['components'])) { ?>
			<tr>
				<td align="center" colspan="3"><?=l('no_components')?></td>
			</tr>
			<? } ?>
		</table>
	</div>
	<?
	}
	
	foot();
}
?>