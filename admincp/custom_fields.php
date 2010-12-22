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
 */

require 'global.php';

// New / Edit field
if(isset($_REQUEST['new']) or isset($_REQUEST['edit']))
{
	// check for errors
	$errors = array();
	if(isset($_POST['action']))
	{
		// check for errors
		if(empty($_POST['name']))
			$errors['name'] = l('error_name_empty');
		if(!count($_POST['project_ids']))
			$errors['projects'] = true;
	}
	
	// Create field
	if(@$_POST['action'] == 'create')
	{
		if(!count($errors))
		{
			$project_ids = implode(',',$_POST['project_ids']);
			
			$db->query("INSERT INTO ".DBPF."custom_fields (id,name,code,project_ids)
				        VALUES(0,'".$db->es($_POST['name'])."','".$db->es($_POST['code'])."','".$project_ids."')");
		}
	}
	
	// Get field data
	if(isset($_REQUEST['edit']))
	{
		$field = $db->fetcharray($db->query("SELECT * FROM ".DBPF."custom_fields WHERE id='".$db->es($_REQUEST['edit'])."' LIMIT 1"));
		$field['projects'] = explode(',',$field['project_ids']);
	}
	
	head(l((isset($_REQUEST['edit']) ? 'Edit' : 'New').'_Custom_Field'),true,'tickets');
	?>
	<?php if(isset($errors) && count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="custom_fields.php?<?php echo (isset($_REQUEST['edit']) ? 'edit='.$_REQUEST['edit'] : 'new')?>" method="post">
	<input type="hidden" name="action" value="<?php echo (isset($_REQUEST['edit']) ? 'save' : 'create')?>" />
	<div class="thead"><?php echo l((isset($_REQUEST['edit']) ? 'Edit' : 'New').'_Custom_Field')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('custom_field_name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?php echo (isset($field['name']) ? $field['name'] :'')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('Field_Code')?></td>
			</tr>
			<tr class="<?php echo altbg(); ?>">
				<td colspan="2"><?php echo l('custom_field_code_description'); ?></td>
			<tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="code" style="width:100%;height:200px"><?php echo (isset($_POST['code']) ? $_POST['code'] : @$field['code'])?></textarea></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('projects')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('custom_field_project_description')?></td>
				<td width="200">
					<select name="project_ids[]" multiple="multiple" style="width:100%;height:50px;">
						<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif(isset($field) && in_array($project['id'],$field['projects']),' selected="selected"')?>><?php echo $project['name']?></option>
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
// Delete field
elseif(isset($_REQUEST['delete']))
{
	
}
// List fields
else
{
	// Fetch custom fields
	$fields = array();
	$get = $db->query("SELECT * FROM ".DBPF."custom_fields ORDER BY name ASC");
	while($info = $db->fetcharray($get))
		$fields[] = $info;
	
	head(l('Custom_Fields'),true,'tickets');
	?>
	<h2><?php echo l('Custom_Fields'); ?></h2>
	
	<div class="thead"><?php echo l('Custom_Fields'); ?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('Field')?></th>
				<th></th>
			</tr>
	<?php foreach($fields as $field) { ?>
			<tr>
				<td><a href="custom_fields.php?edit=<?php echo $field['id']; ?>"><?php echo $field['name']?></a></td>
				<td align="right">
					<a href="custom_fields.php?edit=<?php echo $field['id']; ?>"><img src="images/pencil.png" alt="<?php echo l('edit'); ?>" title="<?php echo l('edit'); ?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$field['name'])?>')) { window.location = 'custom_fields.php?delete=<?php echo $field['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
	<?php } ?>
		</table>
	</div>
	<?php
	foot();
}