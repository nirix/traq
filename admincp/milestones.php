<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

require("global.php");

if(!$user->group->isadmin) {
	exit;
}

if($_REQUEST['action'] == "manage" || $_REQUEST['action'] == '') {
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPREFIX."projects ORDER BY name ASC");
	while($project = $db->fetcharray($fetchprojects)) {
		$project['milestones'] = array();
		$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project='".$project['id']."' ORDER BY project ASC");
		while($info = $db->fetcharray($fetchmilestones)) {
			$info['projectinfo'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE id='".$info['project']."' LIMIT 1"));
			$project['milestones'][] = $info;
		}
		$projects[] = $project;
	}
	

	
	adminheader('Milestones');
	?>
	<div id="content">
		<div class="content-group">
			<div class="content-title">Milestones</div>
			<table width="100%" class="componentlist" cellspacing="0" cellpadding="4">
				<thead>
					<th class="component">Milestone (codename)</th>
					<th class="project">Project</th>
					<th class="actions">Actions</th>
				</thead>
				<? foreach($projects as $project) { ?>
				<tr class="thead">
					<td colspan="3"><?=$project['name']?></td>
				</tr>
				<? foreach($project['milestones'] as $milestone) { ?>
				<tr>
					<td class="component"><a href="milestones.php?action=modify&milestone=<?=$milestone['id']?>"><?=$milestone['milestone']?> <?=(!empty($milestone['codename']) ? '('.$milestone['codename'].')' : '')?></a></td>
					<td class="project"><?=$milestone['projectinfo']['name']?></td>
					<td class="actions"><a href="javascript:void(0);" onclick="if(confirm('Are you sure you want to delete milestone <?=$milestone['milestone']?> for project: <?=$milestone['projectinfo']['name']?>')) { window.location='milestones.php?action=delete&milestone=<?=$milestone['id']?>' }">Delete</a></td>
				</tr>
				<? } ?>
				<? } ?>
			</table>
		</div>
	</div>
	<?
	adminfooter();
} elseif($_REQUEST['action'] == "new") {
	if($_POST['do'] == "create") {
		$errors = array();
		if($_POST['milestone'] == "") {
			$errors['milestone'] = "You must enter a Milestone/Name";
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		if(!empty($_POST['dueday']) && !empty($_POST['duemonth']) && !empty($_POST['dueyear'])) {
			$due = mktime(0,0,0,$_POST['duemonth'],$_POST['dueday'],$_POST['dueyear']);
		} else {
			$due = 0;
		}
		$db->query("INSERT INTO ".DBPREFIX."milestones VALUES(0,
															'".$db->escapestring($_POST['milestone'])."',
															'".$db->escapestring($_POST['codename'])."',
															'".$db->escapestring($_POST['description'])."',
															".$db->escapestring($_POST['project']).",
															".$due.",
															0
															)");
		header("Location: milestones.php?action=manage");
	} else {
		adminheader('New Milestone');
		?>
		<div id="content">
			<form action="milestones.php?action=new" method="post">
			<input type="hidden" name="do" value="create" />
			<div class="content-group">
				<div class="content-title">New Milestone</div>
				<? if(count($errors)) { ?>
				<div class="content-group-content">
					<div class="errormessage">
					<? foreach($errors as $error) { ?>
					<?=$error?><br />
					<? } ?>
					</div>
				</div>
				<? } ?>
				<table width="700">
					<tr valign="top">
						<th>Milestone/Name</th>
						<td><input type="text" name="milestone" /></td>
					</tr>
					<tr valign="top">
						<th>Codename</th>
						<td><input type="text" name="codename" /></td>
					</tr>
					<tr valign="top">
						<th>Description</th>
						<td><textarea name="description" rows="10" cols="50"></textarea></td>
					</tr>
					<tr valign="top">
						<th>Due Date <small>(DD/MM/YYYY)</small><br /><small>Leave blank for no date.</small></th>
						<td>
							<input type="text" name="dueday" value="<?=date("d")?>" maxlength="2" style="width:25px;" />/<input type="text" name="duemonth" value="<?=(date("m") == 12 ? 1 : date("m")+1)?>" maxlength="2" style="width:25px;" />/<input type="text" name="dueyear" value="<?=date("Y")?>" maxlength="4" style="width:45px;" />
						</td>
					</tr>
					<tr valign="top">
						<th>Project</th>
						<td>
							<select name="project">
								<? foreach(getprojects() as $project) { ?>
								<option value="<?=$project['id']?>"><?=$project['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Create Milestone</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
} elseif($_REQUEST['action'] == "modify") {
	if($_POST['do'] == "modify") {
		$errors = array();
		if($_POST['milestone'] == "") {
			$errors['milestone'] = "You must enter a Milestone/Name";
		}
	}
	
	if(!count($errors) && isset($_POST['do'])) {
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$db->escapestring($_POST['milestoneid'])."' LIMIT 1"));
		if(!empty($_POST['dueday']) && !empty($_POST['duemonth']) && !empty($_POST['dueyear'])) {
			$due = mktime(0,0,0,$_POST['duemonth'],$_POST['dueday'],$_POST['dueyear']);
		} else {
			$due = 0;
		}
		if($milestone['completed'] > 0 && $_POST['completed'] == 1) {
			$completed = $milestone['completed'];
		} elseif($milestone['completed'] == 0 && $_POST['completed'] == 1) {
			$completed = mktime(0,0,0,date("m"),date("d"),date("Y"));
		} else {
			$completed = 0;
		}
		$db->query("UPDATE ".DBPREFIX."milestones SET milestone='".$db->escapestring($_POST['milestone'])."', codename='".$db->escapestring($_POST['codename'])."', ".DBPREFIX."milestones.desc='".$db->escapestring($_POST['description'])."', project='".$db->escapestring($_POST['project'])."', due='".$due."', completed='".$completed."' WHERE id='".$db->escapestring($_POST['milestoneid'])."' LIMIT 1");
		header("Location: milestones.php?action=manage");
	} else {
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$db->escapestring($_REQUEST['milestone'])."' LIMIT 1"));
		adminheader('Modify Milestone');
		?>
		<div id="content">
			<form action="milestones.php?action=modify" method="post">
			<input type="hidden" name="do" value="modify" />
			<input type="hidden" name="milestoneid" value="<?=$milestone['id']?>" />
			<div class="content-group">
				<div class="content-title">Modify Milestone</div>
				<? if(count($errors)) { ?>
				<div class="content-group-content">
					<div class="errormessage">
					<? foreach($errors as $error) { ?>
					<?=$error?><br />
					<? } ?>
					</div>
				</div>
				<? } ?>
				<table width="700">
					<tr valign="top">
						<th>Milestone/Name</th>
						<td><input type="text" name="milestone" value="<?=$milestone['milestone']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Codename</th>
						<td><input type="text" name="codename" value="<?=$milestone['codename']?>" /></td>
					</tr>
					<tr valign="top">
						<th>Description</th>
						<td><textarea name="description" rows="10" cols="50"><?=stripslashes($milestone['desc'])?></textarea></td>
					</tr>
					<tr valign="top">
						<th>Due Date <small>(DD/MM/YYYY)</small><br /><small>Leave blank for no date.</small></th>
						<td>
							<input type="text" name="dueday" value="<?=($milestone['due'] > 0 ? date("d",$milestone['due']) : '')?>" maxlength="2" style="width:25px;" />/<input type="text" name="duemonth" value="<?=($milestone['due'] > 0 ? date("m",$milestone['due']) : '')?>" maxlength="2" style="width:25px;" />/<input type="text" name="dueyear" value="<?=($milestone['due'] > 0 ? date("Y",$milestone['due']) : '')?>" maxlength="4" style="width:45px;" />
						</td>
					</tr>
					<tr valign="top">
						<th>Mark Completed</th>
						<td>
							<input type="checkbox" name="completed" value="1"<?=($milestone['completed'] > 0 ? ' checked="checked"' : '')?> />
						</td>
					</tr>
					<tr valign="top">
						<th>Project</th>
						<td>
							<select name="project">
								<? foreach(getprojects() as $project) { ?>
								<option value="<?=$project['id']?>"<?=($milestone['project'] == $project['id'] ? ' selected="selected"' : '')?>><?=$project['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th></th>
						<td><button type="submit">Update Milestone</button></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
		<?
		adminfooter();
	}
} elseif($_REQUEST['action'] == "delete") {
	$db->query("DELETE FROM ".DBPREFIX."milestones WHERE id='".$db->escapestring($_REQUEST['milestone'])."' LIMIT 1");
	header("Location: milestones.php");
}
?>