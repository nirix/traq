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

// New/Edit Component
if(isset($_REQUEST['new']) or isset($_REQUEST['edit']))
{
	// Check for errors...
	if(isset($_POST['action']))
	{
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_component_name_blank');
		if(empty($_POST['project']))
			$errors['project'] = l('error_project_blank');
	}
	
	// Create the component
	if($_POST['action'] == 'create')
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
	
	// Save the component
	if($_POST['action'] == 'save')
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."components SET
				name='".$db->res($_POST['name'])."',
				project_id='".$db->res($_POST['project'])."'
				WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
			
			header("Location: components.php");
		}
	
	if(isset($_REQUEST['edit']))
		$component = $db->queryfirst("SELECT * FROM ".DBPF."components WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
	else
		$component = $_POST;
	
	head(l((isset($_REQUEST['new']) ? 'new' : 'edit').'_component'),true,'projects');
	?>
	<?php if(isset($error) && count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="components.php?<?php echo (isset($_REQUEST['edit']) ? 'edit='.$_REQUEST['edit'] : 'new')?>" method="post">
	<input type="hidden" name="action" value="<?php echo (isset($_REQUEST['edit']) ? 'save' : 'create')?>" />
	<div class="thead"><?php echo l((isset($_REQUEST['new']) ? 'new' : 'edit').'_component')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('component_name_description')?></td>
				<td align="right"><input type="text" name="name" value="<?php echo $component['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('component_project_description')?></td>
				<td align="right">
					<select name="project">
					<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif($project['id'] == $component['project'],' selected="selected"')?>><?php echo $project['name']?></option>
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
	<h2><?php echo l('components')?></h2>
	
	<form action="components.php?new" method="post">
	<input type="hidden" name="action" value="create" />
	<div class="thead"><?php echo l('new_component')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('name')?></th>
				<th width="200" align="left"><?php echo l('project')?></th>
				<th></th>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><input type="text" name="name" /></td>
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
				<th width="200" align="left"><?php echo l('component')?></th>
				<th></th>
			</tr>
			<?php foreach($project['components'] as $component) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="components.php?edit=<?php echo $component['id']?>"><?php echo $component['name']?></a></td>
				<td align="right">
					<a href="components.php?edit=<?php echo $component['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$component['name'])?>')) { window.location = 'components.php?delete=<?php echo $component['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($project['components'])) { ?>
			<tr>
				<td align="center" colspan="3"><?php echo l('no_components')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	}
	foot();
}
?>