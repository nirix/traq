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
	<? if(count($errors)) { ?>
	<div class="message error">
		<? foreach($errors as $error) { ?>
		<?=$error?><br />
		<? } ?>
	</div>
	<? } ?>
	<form action="milestones.php?new" method="post">
	<div class="thead"><?=l('new_milestone')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('milestone')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_name_description')?></td>
				<td align="right"><input type="text" name="milestone" value="<?=$_POST['milestone']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('slug')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_slug_description')?></td>
				<td align="right"><input type="text" name="slug" value="<?=$_POST['slug']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('codename')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_codename_description')?></td>
				<td align="right"><input type="text" name="codename" value="<?=$_POST['codename']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_project_description')?></td>
				<td align="right">
					<select name="project">
					<? foreach(getprojects() as $project) { ?>
						<option value="<?=$project['id']?>"<?=iif($project['id'] == $_POST['project'],' selected="selected"')?>><?=$project['name']?></option>
					<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('due')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_due_description')?></td>
				<td align="right"><input type="text" name="due" value="<?=$_POST['due']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('display_order')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_displayorder_description')?></td>
				<td align="right"><input type="text" name="displayorder" value="<?=($_POST['displayorder'] ? $_POST['displayorder'] : '0')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('milestone_description')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td colspan="2"><textarea name="info" style="width:100%;height:200px"><?=$_POST['info']?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?=l('create')?>" /></div>
	</div>
	</form>
	<?
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
			
			if($_POST['status'] == 'active')
			{
				$completed = 0;
				$cancelled = 0;
				$locked = 0;
			}
			elseif($_POST['status'] == 'completed')
			{
				$completed = time();
				$cancelled = 0;
				$locked = 1;
			}
			elseif($_POST['status'] = 'cancelled')
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
	<? if(count($errors)) { ?>
	<div class="message error">
		<? foreach($errors as $error) { ?>
		<?=$error?><br />
		<? } ?>
	</div>
	<? } ?>
	<form action="milestones.php?edit=<?=$_REQUEST['edit']?>" method="post">
	<div class="thead"><?=l('edit_milestone')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('milestone')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_name_description')?></td>
				<td align="right"><input type="text" name="milestone" value="<?=$milestone['milestone']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('slug')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_slug_description')?></td>
				<td align="right"><input type="text" name="slug" value="<?=$milestone['slug']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('codename')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_codename_description')?></td>
				<td align="right"><input type="text" name="codename" value="<?=$milestone['codename']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_project_description')?></td>
				<td align="right">
					<select name="project">
					<? foreach(getprojects() as $project) { ?>
						<option value="<?=$project['id']?>"<?=iif($project['id'] == $milestone['project_id'],' selected="selected"')?>><?=$project['name']?></option>
					<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('status')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_status_description')?></td>
				<td align="right">
					<select name="status">
						<option value="active"<?=(!$milestone['locked'] ? ' selected="selected"' : '')?>><?=l('active')?></option>
						<option value="completed"<?=($milestone['locked'] && $milestone['completed'] > 0 ? ' selected="selected"' : '')?>><?=l('completed')?></option>
						<option value="cancelled"<?=($milestone['locked'] && $milestone['cancelled'] > 0 ? ' selected="selected"' : '')?>><?=l('cancelled')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('due')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_due_description')?></td>
				<td align="right"><input type="text" name="due" value="<?=($milestone['due'] > 0 ? $milestone['due'] : '')?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('display_order')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_displayorder_description')?></td>
				<td align="right"><input type="text" name="displayorder" value="<?=$milestone['displayorder']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('milestone_description')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td colspan="2"><textarea name="info" style="width:100%;height:150px"><?=$milestone['info']?></textarea></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('milestone_changelog')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td colspan="2"><textarea name="changelog" style="width:100%;height:150px"><?=$milestone['changelog']?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?=l('update')?>" /></div>
	</div>
	</form>
	<?
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
	<h2><?=l('milestones')?></h2>
	<?
	
	foreach($projects as $project) {
	?>
	<div class="thead"><?=$project['name']?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?=l('milestone')?></th>
				<th width="200"><?=l('codename')?></th>
				<th></th>
			</tr>
			<? foreach($project['milestones'] as $milestone) { ?>
			<tr>
				<td><a href="milestones.php?edit=<?=$milestone['id']?>"><?=$milestone['milestone']?></a></td>
				<td align="center"><?=$milestone['codename']?></td>
				<td align="right">
					<select>
						<option selected="selected">Actions</option>
						<option onclick="if(confirm('<?=l('confirm_delete_milestone_x',$milestone['milestone'])?>')) { window.location='milestones.php?delete=<?=$milestone['id']?>'; }"><?=l('delete')?></option>
					</select>
				</td>
			</tr>
			<? } ?>
			<? if(!count($project['milestones'])) { ?>
			<tr>
				<td align="center" colspan="3"><?=l('no_milestones')?></td>
			</tr>
			<? } ?>
		</table>
	</div>
	<?
	}
	
	foot();
}
?>