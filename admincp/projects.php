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
	// Check if the form has been submit.
	if($_POST['action'] == 'create')
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_project_name_blank');
		if(empty($_POST['slug']))
			$errors['slug'] = l('error_project_slug_blank');
		if($db->numrows($db->query("SELECT slug FROM ".DBPF."projects WHERE slug='".$db->res($_POST['slug'])."' LIMIT 1")))
			$errors['slug'] = l('error_project_slug_taken');
		
		if(!count($errors))
		{
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
			header("Location: projects.php");
		}
	}
	
	// Display the form.
	head(l('new_project'),true,sidebar_links('projects'));
	?>
	<? if(count($errors)) { ?>
	<div class="message error">
		<? foreach($errors as $error) { ?>
		<?=$error?><br />
		<? } ?>
	</div>
	<? } ?>
	<form action="projects.php?new" method="post">
	<input type="hidden" name="action" value="create" />
	<div class="thead"><?=l('new_project')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('project_name')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('project_name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?=$_POST['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('codename')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('codename_description')?></td>
				<td width="200"><input type="text" name="codename" value="<?=$_POST['codename']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('slug')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('project_slug_description')?></td>
				<td width="200"><input type="text" name="slug" value="<?=$_POST['slug']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project_managers')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td valign="top"><?=l('project_managers_description')?></td>
				<td width="200">
					<select name="managers[]" multiple="multiple" style="width:100%;height:50px;">
						<? foreach($user->getusers() as $userinfo) { ?>
						<option value="<?=$userinfo['id']?>"><?=$userinfo['username']?></option>
						<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('display_order')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('project_dispay_order_description')?></td>
				<td width="200"><input type="text" name="displayorder" value="<?=(isset($_POST['displayorder']) ? $_POST['displayorder'] : 0)?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project_description')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td colspan="2"><textarea name="info" style="width:100%;height:250px"><?=$_POST['info']?></textarea></td>
			</tr>
			<tr>
				<td colspan="2" class="tfoot"><div align="center"><input type="submit" value="<?=l('create')?>" /></div></td>
			</tr>
		</table>
	</div>
	</form>
	<?
	foot();
}
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
	
	head(l('edit_project'),true,sidebar_links('projects'));
	?>
	<? if(count($errors)) { ?>
	<div class="message error">
		<? foreach($errors as $error) { ?>
		<?=$error?><br />
		<? } ?>
	</div>
	<? } ?>
	<form action="projects.php?edit&amp;project=<?=$_REQUEST['project']?>" method="post">
	<div class="thead"><?=l('edit_project')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('project_name')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('project_name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?=$project['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('codename')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('codename_description')?></td>
				<td width="200"><input type="text" name="codename" value="<?=$project['codename']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('slug')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('project_slug_description')?></td>
				<td width="200"><input type="text" name="slug" value="<?=$project['slug']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project_managers')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td valign="top"><?=l('project_managers_description')?></td>
				<td width="200">
					<select name="managers[]" multiple="multiple" style="width:100%;height:50px;">
						<? foreach($user->getusers() as $userinfo) { ?>
						<option value="<?=$userinfo['id']?>"<?=iif(in_array($userinfo['id'],$project['managers']),' selected="selected"')?>><?=$userinfo['username']?></option>
						<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('display_order')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('project_dispay_order_description')?></td>
				<td width="200"><input type="text" name="displayorder" value="<?=(isset($project['displayorder']) ? $project['displayorder'] : 0)?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project_description')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td colspan="2"><textarea name="info" style="width:100%;height:250px"><?=$project['info']?></textarea></td>
			</tr>
			<tr>
				<td colspan="2" class="tfoot"><div align="center"><input type="submit" value="<?=l('update')?>" /></div></td>
			</tr>
		</table>
	</div>
	</form>
	<?
	foot();
}
else
{
	// Get projects
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects))
	{
		$projects[] = $info;
	}
	
	head(l('projects'),true,$sidebar_links['projects']['links']);
	?>
	<div class="thead"><?=l('projects')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?=l('project')?></th>
				<th width="200"><?=l('codename')?></th>
				<th></th>
			</tr>
			<? foreach($projects as $project) { ?>
			<tr>
				<td><a href="projects.php?edit&project=<?=$project['id']?>"><?=$project['name']?></a></td>
				<td align="center"><?=$project['codename']?></td>
				<td align="right">
					
				</td>
			</tr>
			<? } ?>
			<? if(!count($projects)) { ?>
			<tr>
				<td align="center" colspan="3"><?=l('no_plugins')?></td>
			</tr>
			<? } ?>
		</table>
	</div>
	<?
	
	foot();
}
?>