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

// New Milestone
if(isset($_REQUEST['new']))
{
	// Create the milestone
	if(isset($_POST['milestone']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['milestone']))
			$errors['milestone'] = l('error_milestone_name_blank');
		if(empty($_POST['slug']))
			$errors['slug'] = l('error_milestone_slug_blank');
		if($db->numrows($db->query("SELECT slug FROM ".DBPF."milestones WHERE slug='".$db->res($_POST['slug'])."' AND project_id != '".$db->res($_POST['project'])."' LIMIT 1")))
			$errors['slug'] = l('error_milestone_slug_taken');
		if(empty($_POST['project']))
			$errors['project'] = l('error_project_blank');
		
		if(!count($errors))
		{
			$due = 0;
			if(!empty($_POST['due']))
			{
				$due = explode('/',$_POST['due']);
				$due = mktime(12,00,00,$due[1],$due[0],$due[2]);
			}
			
			$db->query("INSERT INTO ".DBPF."milestones
			(milestone,codename,slug,info,changelog,due,project_id,displayorder)
			VALUES(
			'".$db->res($_POST['milestone'])."',
			'".$db->res($_POST['codename'])."',
			'".$db->res($_POST['slug'])."',
			'".$db->res($_POST['info'])."',
			'',
			'".$due."',
			'".$db->res($_POST['project'])."',
			'".$db->res($_POST['displayorder'])."'
			)");
			
			header("Location: milestones.php");
		}
	}
	
	head(l('new_milestone'),true,'projects');
	?>
	<?php if(count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="milestones.php?new" method="post">
	<div class="thead"><?php echo l('new_milestone')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('milestone')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_name_description')?></td>
				<td align="right"><input type="text" name="milestone" value="<?php echo $_POST['milestone']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('slug')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_slug_description')?></td>
				<td align="right"><input type="text" name="slug" value="<?php echo $_POST['slug']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('codename')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_codename_description')?></td>
				<td align="right"><input type="text" name="codename" value="<?php echo $_POST['codename']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_project_description')?></td>
				<td align="right">
					<select name="project">
					<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif($project['id'] == $_POST['project'],' selected="selected"')?>><?php echo $project['name']?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('due')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_due_description')?></td>
				<td align="right"><input type="text" name="due" value="<?php echo $_POST['due']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('display_order')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_displayorder_description')?></td>
				<td align="right"><input type="text" name="displayorder" value="<?php echo ($_POST['displayorder'] ? $_POST['displayorder'] : '0')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('milestone_description')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="info" style="width:100%;height:200px"><?php echo $_POST['info']?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('create')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Edit Milestone
elseif(isset($_REQUEST['edit']))
{
	$milestone = $db->queryfirst("SELECT * FROM ".DBPF."milestones WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
	
	// Save the milestone
	if(isset($_POST['milestone']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['milestone']))
			$errors['milestone'] = l('error_milestone_name_blank');
		if(empty($_POST['slug']))
			$errors['slug'] = l('error_milestone_slug_blank');
		if($db->numrows($db->query("SELECT slug FROM ".DBPF."milestones WHERE slug='".$db->res($_POST['slug'])."' AND project_id != '".$db->res($_POST['project'])."' AND id != '".$db->res($_REQUEST['edit'])."' LIMIT 1")))
			$errors['slug'] = l('error_milestone_slug_taken');
		if(empty($_POST['project']))
			$errors['project'] = l('error_project_blank');
		
		if(!count($errors))
		{
			$due = 0;
			if(!empty($_POST['due']))
			{
				$due = explode('/',$_POST['due']);
				$due = mktime(12,00,00,$due[1],$due[0],$due[2]);
			}
			
			$completed = $milestone['completed'];
			$cancelled = $milestone['cancelled'];
			$locked = $milestone['locked'];
			if($_POST['status'] == 'active')
			{
				$completed = 0;
				$cancelled = 0;
				$locked = 0;
			}
			elseif($_POST['status'] == 'completed' and !$milestone['completed'])
			{
				$completed = time();
				$cancelled = 0;
				$locked = 1;
			}
			elseif($_POST['status'] == 'cancelled' and !$milestone['cancelled'])
			{
				$completed = 0;
				$cancelled = time();
				$locked = 1;
			}
			
			$db->query("UPDATE ".DBPF."milestones SET
				milestone='".$db->res($_POST['milestone'])."',
				slug='".$db->res($_POST['slug'])."',
				codename='".$db->res($_POST['codename'])."',
				info='".$db->res($_POST['info'])."',
				milestone='".$db->res($_POST['milestone'])."',
				due='".$db->res($due)."',
				completed='".$db->res($completed)."',
				cancelled='".$db->res($cancelled)."',
				locked='".$db->res($locked)."',
				changelog='".$db->res($_POST['changelog'])."',
				project_id='".$db->res($_POST['project'])."',
				displayorder='".$db->res($_POST['displayorder'])."'
				WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
			
			header("Location: milestones.php?updated");
		}
	}
	
	head(l('edit_milestone'),true,'projects');
	?>
	<?php if(count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="milestones.php?edit=<?php echo $_REQUEST['edit']?>" method="post">
	<div class="thead"><?php echo l('edit_milestone')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('milestone')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_name_description')?></td>
				<td align="right"><input type="text" name="milestone" value="<?php echo $milestone['milestone']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('slug')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_slug_description')?></td>
				<td align="right"><input type="text" name="slug" value="<?php echo $milestone['slug']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('codename')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_codename_description')?></td>
				<td align="right"><input type="text" name="codename" value="<?php echo $milestone['codename']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_project_description')?></td>
				<td align="right">
					<select name="project">
					<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif($project['id'] == $milestone['project_id'],' selected="selected"')?>><?php echo $project['name']?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('status')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_status_description')?></td>
				<td align="right">
					<select name="status">
						<option value="active"<?php echo (!$milestone['locked'] ? ' selected="selected"' : '')?>><?php echo l('active')?></option>
						<option value="completed"<?php echo ($milestone['locked'] && $milestone['completed'] > 0 ? ' selected="selected"' : '')?>><?php echo l('completed')?></option>
						<option value="cancelled"<?php echo ($milestone['locked'] && $milestone['cancelled'] > 0 ? ' selected="selected"' : '')?>><?php echo l('cancelled')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('due')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_due_description')?></td>
				<td align="right"><input type="text" name="due" value="<?php echo ($milestone['due'] > 0 ? date("d/m/Y",$milestone['due']) : '')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('display_order')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('milestone_displayorder_description')?></td>
				<td align="right"><input type="text" name="displayorder" value="<?php echo $milestone['displayorder']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('milestone_description')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="info" style="width:100%;height:150px"><?php echo $milestone['info']?></textarea></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('milestone_changelog')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="changelog" style="width:100%;height:150px"><?php echo $milestone['changelog']?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Delete Milestone
elseif(isset($_REQUEST['delete']))
{
	// Delete milstone
	$milestone = $db->queryfirst("SELECT id,project_id FROM ".DBPF."milestones WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	$db->query("DELETE FROM ".DBPF."milestones WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	
	// Update milestone tickets
	$newmilestone = $db->queryfirst("SELECT id FROM ".DBPF."milestones WHERE locked='0' AND project_id='".$milestone['project_id']."' ORDER BY id ASC LIMIT 1");
	$db->query("UPDATE ".DBPF."tickets SET milestone_id='".$newmilestone['id']."' WHERE milestone_id='".$milestone['id']."'");

	header("Loaction: milestones.php?deleted");
}
// List Milestones
else
{
	// Get Milestones
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects))
	{
		$info['milestones'] = array();
		$fetchmilestones = $db->query("SELECT * FROM ".DBPF."milestones WHERE project_id='".$info['id']."' ORDER BY milestone ASC");
		while($milestone = $db->fetcharray($fetchmilestones))
		{
			$info['milestones'][] = $milestone;
		}
		
		$projects[] = $info;
	}
	
	head(l('milestones'),true,'projects');
	?>
	<h2><?php echo l('milestones')?></h2>
	<?php
	foreach($projects as $project) {
	?>
	<div class="thead"><?php echo $project['name']?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('milestone')?></th>
				<th width="200"><?php echo l('codename')?></th>
				<th></th>
			</tr>
			<?php foreach($project['milestones'] as $milestone) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="milestones.php?edit=<?php echo $milestone['id']?>"><?php echo $milestone['milestone']?></a></td>
				<td align="center"><?php echo $milestone['codename']?></td>
				<td align="right">
					<a href="milestones.php?edit=<?php echo $milestone['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$milestone['milestone'])?>')) { window.location='milestones.php?delete=<?php echo $milestone['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($project['milestones'])) { ?>
			<tr>
				<td align="center" colspan="3"><?php echo l('no_milestones')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	}
	foot();
}
?>