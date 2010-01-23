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
elseif(isset($_REQUEST['edit']))
{

}
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
			<tr>
				<td><a href="component.php?edit&$component=<?=$component['id']?>"><?=$component['name']?></a></td>
				<td align="right">
					
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