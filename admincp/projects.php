<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Set the full path to the Traq folder
define('TRAQPATH',str_replace(pathinfo('../index.php',PATHINFO_BASENAME),'','../index.php'));

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
			$db->query("INSERT INTO ".DBPF."projects VALUES(
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
	head(l('new_project'));
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
	<div class="tborder">
		<div class="thead"><?=l('project_info')?></div>
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project_name')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('admin_project_name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?=$_POST['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('codename')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('admin_codename_description')?></td>
				<td width="200"><input type="text" name="codename" value="<?=$_POST['codename']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('slug')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('admin_project_slug_description')?></td>
				<td width="200"><input type="text" name="slug" value="<?=$_POST['slug']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('project_managers')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('admin_project_managers_description')?></td>
				<td width="200"><input type="text" name="displayorder" value="<?=(isset($_POST['displayorder']) ? $_POST['displayorder'] : 0)?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('display_order')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('admin_project_dispay_order_description')?></td>
				<td width="200">
					<select name="managers[]" multiple="multiple" style="width:100%;height:50px;">
						<? foreach($user->getusers() as $userinfo) { ?>
						<option value="<?=$userinfo['id']?>"><?=$userinfo['username']?></option>
						<? } ?>
					</select>
				</td>
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
?>