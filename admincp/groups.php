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

// New and Edit Usergroup
if(isset($_REQUEST['edit']) or isset($_REQUEST['new']))
{
	// Create
	if(@$_POST['action'] == 'create')
	{
		// Check for errors
		$errors = array();
		if(empty($_POST['values']['name']))
			$errors['name'] = l('error_name_empty');
			
		if(!count($errors))
		{
			// Sort columns from values
			$keys = array();
			$values = array();
			foreach($_POST['values'] as $key => $val)
			{
				$keys[] = $key;
				$values[] = "'".$val."'";
			}
			
			$db->query("INSERT INTO ".DBPF."usergroups (".implode(',',$keys).") VALUES(".implode(',',$values).")");
			
			header("Location: groups.php?created");
		}
		
		$group = $_POST['values'];
	}
	
	// Save Usergroup
	if(@$_POST['action'] == 'save')
	{
		// Check for errors
		$errors = array();
		if(empty($_POST['values']['name']))
			$errors['name'] = l('error_name_empty');
			
		if(!count($errors))
		{
			// Make the query.
			$query = array();
			foreach($_POST['values'] as $key => $val)
				$query[] = $key."='".$val."'";
			
			// Run the query.
			$db->query("UPDATE ".DBPF."usergroups SET ".implode(', ',$query)." WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
			
			header("Location: groups.php?saved");
		}
	}
	
	if(isset($_REQUEST['edit']))
		$group = $db->queryfirst("SELECT * FROM ".DBPF."usergroups WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
	
	head(l((isset($_REQUEST['edit']) ? 'edit' : 'new').'_usergroup'),true,'users');
	?>
	<?php if(count(@$errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="groups.php?<?php echo (isset($_REQUEST['edit']) ? 'edit='.$_REQUEST['edit'] : 'new')?>" method="post">
	<input type="hidden" name="action" value="<?php echo (isset($_REQUEST['edit']) ? 'save' : 'create')?>" />
	<div class="thead"><?php echo l((isset($_REQUEST['edit']) ? 'edit' : 'new').'_usergroup')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('usergroup_name_description')?></td>
				<td width="200"><input type="text" name="values[name]" value="<?php echo @$group['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('administrator')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('usergroup_admin_description')?></td>
				<td width="200">
					<input type="radio" name="values[is_admin]" value="1" id="is_admin_yes"<?php echo iif(@$group['is_admin'],' checked="checked"')?> /> <label for="is_admin_yes"><?php echo l('yes')?></label>
					<input type="radio" name="values[is_admin]" value="0" id="is_admin_no"<?php echo iif(!@$group['is_admin'],' checked="checked"')?> /> <label for="is_admin_no"><?php echo l('no')?></label>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('create_tickets')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('usergroup_create_tickets_description')?></td>
				<td width="200">
					<input type="radio" name="values[create_tickets]" value="1" id="create_tickets_yes"<?php echo iif(@$group['create_tickets'],' checked="checked"')?> /> <label for="create_tickets_yes"><?php echo l('yes')?></label>
					<input type="radio" name="values[create_tickets]" value="0" id="create_tickets_no"<?php echo iif(!@$group['create_tickets'],' checked="checked"')?> /> <label for="create_tickets_no"><?php echo l('no')?></label>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('update_tickets')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('usergroup_update_tickets_description')?></td>
				<td width="200">
					<input type="radio" name="values[update_tickets]" value="1" id="update_tickets_yes"<?php echo iif(@$group['update_tickets'],' checked="checked"')?> /> <label for="update_tickets_yes"><?php echo l('yes')?></label>
					<input type="radio" name="values[update_tickets]" value="0" id="update_tickets_no"<?php echo iif(!@$group['update_tickets'],' checked="checked"')?> /> <label for="update_tickets_no"><?php echo l('no')?></label>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('comment_on_tickets')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('usergroup_comment_on_tickets_description')?></td>
				<td width="200">
					<input type="radio" name="values[comment_tickets]" value="1" id="comment_tickets_yes"<?php echo iif(@$group['comment_tickets'],' checked="checked"')?> /> <label for="comment_tickets_yes"><?php echo l('yes')?></label>
					<input type="radio" name="values[comment_tickets]" value="0" id="comment_tickets_no"<?php echo iif(!@$group['comment_tickets'],' checked="checked"')?> /> <label for="comment_tickets_no"><?php echo l('no')?></label>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('delete_tickets')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('usergroup_delete_tickets_description')?></td>
				<td width="200">
					<input type="radio" name="values[delete_tickets]" value="1" id="delete_tickets_yes"<?php echo iif(@$group['delete_tickets'],' checked="checked"')?> /> <label for="delete_tickets_yes"><?php echo l('yes')?></label>
					<input type="radio" name="values[delete_tickets]" value="0" id="delete_tickets_no"<?php echo iif(!@$group['delete_tickets'],' checked="checked"')?> /> <label for="delete_tickets_no"><?php echo l('no')?></label>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('add_attachments')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('usergroup_add_attachments_description')?></td>
				<td width="200">
					<input type="radio" name="values[add_attachments]" value="1" id="add_attachments_yes"<?php echo iif(@$group['add_attachments'],' checked="checked"')?> /> <label for="add_attachments_yes"><?php echo l('yes')?></label>
					<input type="radio" name="values[add_attachments]" value="0" id="add_attachments_no"<?php echo iif(!@$group['add_attachments'],' checked="checked"')?> /> <label for="add_attachments_no"><?php echo l('no')?></label>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l((isset($_REQUEST['edit']) ? 'update' : 'create'))?>" /></div>
	</div>
	<?php
	foot();
}
// Delete Usergroup
elseif(isset($_REQUEST['delete']))
{
	// Make sure we're not trying to delete the default groups...
	if(in_array($_REQUEST['delete'],array(1,2,3))) die();
	
	// Delete Usergroup
	$db->query("DELETE FROM ".DBPF."usergroups WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	
	// Update Users
	$db->query("UPDATE ".DBPF."users SET group_id='2' WHERE group_id='".$db->res($_REQUEST['delete'])."'");

	header("Location: groups.php?deleted");
}
// List Usergroups
else
{
	head(l('usergroups'),true,'users');
	?>
	<div class="thead"><?php echo l('usergroups')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('name')?></th>
				<th></th>
			</tr>
			<?php
			$fetchgroups = $db->query("SELECT id,name FROM ".DBPF."usergroups ORDER BY name ASC");
			while($group = $db->fetcharray($fetchgroups))
			{
			?>
			<tr class="<?php echo altbg()?>">
				<td><a href="groups.php?edit=<?php echo $group['id']?>"><?php echo $group['name']?></a></td>
				<td align="right">
					<a href="groups.php?edit=<?php echo $group['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$group['name'])?>')) { window.location = 'groups.php?delete=<?php echo $group['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	foot();
}
?>