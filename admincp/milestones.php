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
			if(!empty($_POST['due']))
			{
				$due = explode('/',$_POST['due']);
				$due = mktime(12,00,00,$due[1],$due[0],$due[2]);
			}
			
			$db->query("INSERT INTO ".DBPF."milestones
			(milestone,codename,slug,info,due,project_id,displayorder)
			VALUES(
			'".$db->res($_POST['milestone'])."',
			'".$db->res($_POST['codename'])."',
			'".$db->res($_POST['slug'])."',
			'".$db->res($_POST['info'])."',
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
elseif(isset($_REQUEST['edit']))
{
?>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('status')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('milestone_status_description')?></td>
				<td align="right">
					<select name="status">
						<option value="active"><?=l('active')?></option>
						<option value="locked"><?=l('locked')?></option>
						<option value="cancelled"><?=l('cancelled')?></option>
					</select>
				</td>
			</tr>
<?
}
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
				<td><a href="milestones.php?edit&milestone=<?=$milestone['id']?>"><?=$milestone['milestone']?></a></td>
				<td align="center"><?=$milestone['codename']?></td>
				<td align="right">
					
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