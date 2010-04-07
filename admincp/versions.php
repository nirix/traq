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

// New Version
if(isset($_REQUEST['new']))
{
	// Create the version
	if(isset($_POST['name']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_version_name_blank');
		if(empty($_POST['project']))
			$errors['project'] = l('error_project_blank');
		
		if(!count($errors))
		{
			$db->query("INSERT INTO ".DBPF."versions
			(version,project_id)
			VALUES(
			'".$db->res($_POST['name'])."',
			'".$db->res($_POST['project'])."'
			)");
			
			header("Location: versions.php");
		}
	}
	
	head(l('new_version'),true,'projects');
	?>
	<?php if(count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="versions.php?new" method="post">
	<div class="thead"><?php echo l('new_version')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('version_name_description')?></td>
				<td align="right"><input type="text" name="name" value="<?php echo $_POST['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('version_project_description')?></td>
				<td align="right">
					<select name="project">
					<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif($project['id'] == $_POST['project'],' selected="selected"')?>><?php echo $project['name']?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('create')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Edit Version
elseif(isset($_REQUEST['edit']))
{
	$version = $db->queryfirst("SELECT * FROM ".DBPF."versions WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
	
	// Save the version
	if(isset($_POST['name']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_version_name_blank');
		if(empty($_POST['project']))
			$errors['project'] = l('error_project_blank');	
		
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."versions SET
				version='".$db->res($_POST['name'])."',
				project_id='".$db->res($_POST['project'])."'
				WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
			
			header("Location: versions.php");
		}
	}
	
	head(l('edit_version'),true,'projects');
	?>
	<?php if(count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="versions.php?edit=<?php echo $_REQUEST['edit']?>" method="post">
	<div class="thead"><?php echo l('edit_version')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('version_name_description')?></td>
				<td align="right"><input type="text" name="name" value="<?php echo $version['version']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('version_project_description')?></td>
				<td align="right">
					<select name="project">
					<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif($project['id'] == $version['project'],' selected="selected"')?>><?php echo $project['name']?></option>
					<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Delete Version
elseif(isset($_REQUEST['delete']))
{
	$db->query("DELETE FROM ".DBPF."versions WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	$db->query("UPDATE ".DBPF."tickets SET version_id='0' WHERE version_id='".$db->res($_REQUEST['delete'])."'");
	header("Location: versions.php?deleted");
}
// List Versions
else
{
	// Get Versions

	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects))
	{
		$info['versions'] = array();
		$fetchversions = $db->query("SELECT * FROM ".DBPF."versions WHERE project_id='".$info['id']."' ORDER BY version ASC");
		while($version = $db->fetcharray($fetchversions))
		{
			$info['versions'][] = $version;
		}
		
		$projects[] = $info;
	}
	
	head(l('versions'),true,'projects');
	?>
	<h2><?php echo l('versions')?></h2>
	<?php
	foreach($projects as $project) { ?>
	<div class="thead"><?php echo $project['name']?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('version')?></th>
				<th></th>
			</tr>
			<?php foreach($project['versions'] as $version) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="versions.php?edit=<?php echo $version['id']?>"><?php echo $version['version']?></a></td>
				<td align="right">
					<a href="versions.php?edit=<?php echo $version['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_version_x',$version['version'])?>')) { window.location = 'versions.php?delete=<?php echo $version['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($project['versions'])) { ?>
			<tr>
				<td align="center" colspan="3"><?php echo l('no_versions')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	}
	foot();
}
?>