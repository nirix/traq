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

// New Project
if(isset($_REQUEST['new']))
{
	// Check if the form has been submit.
	if(isset($_POST['action']) && $_POST['action'] == 'create')
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_project_name_blank');
		if(empty($_POST['slug']))
			$errors['slug'] = l('error_project_slug_blank');
		if($db->numrows($db->query("SELECT slug FROM ".DBPF."projects WHERE slug='".$db->res($_POST['slug'])."' LIMIT 1")))
			$errors['slug'] = l('error_project_slug_taken');
		if(!count(@$_POST['managers']))
			$errors['managers'] = l('error_select_at_least_one_manager');
		
		if(!count($errors))
		{
			// Insert the project.
			$db->query("INSERT INTO ".DBPF."projects (id,name,slug,codename,info,managers,private,next_tid,displayorder)
				VALUES(
				0,
				'".$db->res($_POST['name'])."',
				'".$db->res($_POST['slug'])."',
				'".$db->res($_POST['codename'])."',
				'".$db->res($_POST['info'])."',
				'".$db->res(implode(',',$_POST['managers']))."',
				0,
				1,
				'".$db->res($_POST['displayorder'])."'
				)");
			$project_id = $db->insertid();
			
			// Create the main wiki page.
			$db->query("INSERT INTO ".DBPF."wiki (project_id,title,slug,body,main)
				VALUES(
				'".$project_id."',
				'Home',
				'home',
				'Welcome to the ".$db->res($_POST['name'])." wiki.',
				'1'
				)");
			header("Location: projects.php");
		}
	}
	
	// Display the form.
	head(l('new_project'),true,'projects');
	?>
	<?php if(isset($errors) && count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="projects.php?new" method="post">
	<input type="hidden" name="action" value="create" />
	<div class="thead"><?php echo l('new_project')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('project_name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('project_name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?php echo (isset($_POST['name']) ? $_POST['name'] :'')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('slug')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('project_slug_description')?></td>
				<td width="200"><input type="text" name="slug" value="<?php echo (isset($_POST['slug']) ? $_POST['slug'] :'')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('codename')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('codename_description')?></td>
				<td width="200"><input type="text" name="codename" value="<?php echo (isset($_POST['codename']) ? $_POST['codename'] :'')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project_managers')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td valign="top"><?php echo l('project_managers_description')?></td>
				<td width="200">
					<select name="managers[]" multiple="multiple" style="width:100%;height:50px;">
						<?php foreach($user->getusers() as $userinfo) { ?>
						<option value="<?php echo $userinfo['id']?>"><?php echo $userinfo['username']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('display_order')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('project_dispay_order_description')?></td>
				<td width="200"><input type="text" name="displayorder" value="<?php echo (isset($_POST['displayorder']) ? $_POST['displayorder'] : 0)?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project_description')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="info" style="width:100%;height:200px"><?php echo (isset($_POST['info']) ? $_POST['info'] :'')?></textarea></td>
			</tr>
			<tr>
				<td colspan="2" class="tfoot"><div align="center"><input type="submit" value="<?php echo l('create')?>" /></div></td>
			</tr>
		</table>
	</div>
	</form>
	<?php
	foot();
}
// Edit Project
elseif(isset($_REQUEST['edit']))
{
	$project = $db->queryfirst("SELECT * FROM ".DBPF."projects WHERE id='".$db->res($_REQUEST['project'])."' LIMIT 1");
	$project['managers'] = explode(',',$project['managers']);
	
	if(isset($_POST['name']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_project_name_blank');
		if(empty($_POST['slug']))
			$errors['slug'] = l('error_project_slug_blank');
		if($db->numrows($db->query("SELECT slug FROM ".DBPF."projects WHERE slug='".$db->res($_POST['slug'])."' AND id != '".$db->res($_REQUEST['project'])."' LIMIT 1")))
			$errors['slug'] = l('error_project_slug_taken');
		if(!count(@$_POST['managers']))
			$errors['managers'] = l('error_select_at_least_one_manager');
		
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."projects SET
			name='".$db->res($_POST['name'])."',
			codename='".$db->res($_POST['codename'])."',
			slug='".$db->res($_POST['slug'])."',
			managers='".$db->res(implode(',',$_POST['managers']))."',
			info='".$db->res($_POST['info'])."',
			displayorder='".$db->res($_POST['displayorder'])."'
			WHERE id='".$db->res($_REQUEST['project'])."' LIMIT 1");
			
			header("Location: projects.php?edit&project=".$_REQUEST['project']."&updated");
		}
	}
	
	head(l('edit_project'),true,'projects');
	?>
	<?php if(isset($errors) && count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="projects.php?edit&amp;project=<?php echo $_REQUEST['project']?>" method="post">
	<div class="thead"><?php echo l('edit_project')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('project_name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('project_name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?php echo $project['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('codename')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('codename_description')?></td>
				<td width="200"><input type="text" name="codename" value="<?php echo $project['codename']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('slug')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('project_slug_description')?></td>
				<td width="200"><input type="text" name="slug" value="<?php echo $project['slug']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project_managers')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td valign="top"><?php echo l('project_managers_description')?></td>
				<td width="200">
					<select name="managers[]" multiple="multiple" style="width:100%;height:50px;">
						<?php foreach($user->getusers() as $userinfo) { ?>
						<option value="<?php echo $userinfo['id']?>"<?php echo iif(in_array($userinfo['id'],$project['managers']),' selected="selected"')?>><?php echo $userinfo['username']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('display_order')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('project_dispay_order_description')?></td>
				<td width="200"><input type="text" name="displayorder" value="<?php echo (isset($project['displayorder']) ? $project['displayorder'] : 0)?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project_description')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="info" style="width:100%;height:250px"><?php echo $project['info']?></textarea></td>
			</tr>
			<tr>
				<td colspan="2" class="tfoot"><div align="center"><input type="submit" value="<?php echo l('update')?>" /></div></td>
			</tr>
		</table>
	</div>
	</form>
	<?php
	foot();
}
// Delete Project
elseif(isset($_REQUEST['delete']))
{
	// Delete the tickets
	$db->query("DELETE FROM ".DBPF."tickets WHERE project_id='".$db->res($_REQUEST['delete'])."'");
	
	// Delete the ticket history/comments
	$db->query("DELETE FROM ".DBPF."ticket_history WHERE project_id='".$db->res($_REQUEST['delete'])."'");
	
	// Delete the timeline
	$db->query("DELETE FROM ".DBPF."timeline WHERE project_id='".$db->res($_REQUEST['delete'])."'");
	
	// Delete the wiki pages
	$db->query("DELETE FROM ".DBPF."wiki WHERE project_id='".$db->res($_REQUEST['delete'])."'");
	
	// Delete the project
	$db->query("DELETE FROM ".DBPF."projects WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	
	header("Location: projects.php?deleted");
}
// List Projects
else
{
	// Get projects
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects))
		$projects[] = $info;
	
	head(l('projects'),true,'projects');
	?>
	<div class="thead"><?php echo l('projects')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('project')?></th>
				<th width="200"><?php echo l('codename')?></th>
				<th></th>
			</tr>
			<?php foreach($projects as $project) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="projects.php?edit&project=<?php echo $project['id']?>"><?php echo $project['name']?></a></td>
				<td align="center"><?php echo $project['codename']?></td>
				<td align="right">
					<a href="projects.php?edit&project=<?php echo $project['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$project['name'])?>')) { window.location = 'projects.php?delete=<?php echo $project['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($projects)) { ?>
			<tr>
				<td align="center" colspan="3"><?php echo l('no_projects')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	foot();
}
?>