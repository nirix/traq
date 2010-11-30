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

// New Wiki page
if(isset($_REQUEST['new']) or isset($_REQUEST['edit']))
{
	// Check for errors
	if(isset($_POST['action']))
	{
		$errors = array();
		if(empty($_POST['title']))
			$errors['title'] = l('error_title_empty');
		if(isset($_REQUEST['edit']) && $db->numrows($db->query("SELECT title FROM ".DBPF."wiki WHERE title='".$db->res($_POST['title'])."' AND id!='".$db->res($_REQUEST['edit'])."' AND project_id='".$db->res($_POST['project_id'])."' LIMIT 1")))
			$errors['title'] = l('error_title_taken');
		if(isset($_REQUEST['new']) && $db->numrows($db->query("SELECT title FROM ".DBPF."wiki WHERE title='".$db->res($_POST['title'])."' AND project_id='".$db->res($_POST['project_id'])."' LIMIT 1")))
			$errors['title'] = l('error_title_taken');
	}
	
	// Create the page
	if(@$_POST['action'] == 'create')
		if(isset($errors) and !count($errors))
		{
			$db->query("INSERT INTO ".DBPF."wiki
				(project_id,title,slug,body)
				VALUES(
				'".$db->res($_POST['project_id'])."',
				'".$db->res($_POST['title'])."',
				'".$db->res(slugit($_POST['title']))."',
				'".$db->res($_POST['body'])."'
				)");
			header("Location: wiki.php?created");
		}
	
	// Update the page
	if(@$_POST['action'] == 'update')
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."wiki SET
				project_id='".$db->res($_POST['project_id'])."',
				title='".$db->res($_POST['title'])."',
				slug='".$db->res(slugit($_POST['title']))."',
				body='".$db->res($_POST['body'])."'
				WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
			header("Location: wiki.php?updated");
		}
	
	// Get the page info
	if(isset($_REQUEST['edit']))
		$page = $db->queryfirst("SELECT * FROM ".DBPF."wiki WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
	
	head(l((isset($_REQUEST['edit']) ? 'Edit' : 'New').'_Wiki_Page'),true,'wiki');
	?>
	<?php if(count(@$errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="wiki.php?<?php echo (isset($_REQUEST['edit']) ? 'edit='.$_REQUEST['edit'] : 'new')?>" method="post">
	<input type="hidden" name="action" value="<?php echo (isset($_REQUEST['edit']) ? 'update' : 'create'); ?>" />
	<div class="thead"><?php echo l((isset($_REQUEST['edit']) ? 'Edit' : 'New').'_Wiki_Page')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('title')?></td>
			</tr>
			<tr>
				<td><?php echo l('page_title_description')?></td>
				<td align="right"><input type="text" name="title" value="<?php echo @$page['title']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('project')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('page_project_description')?></td>
				<td width="200" align="right">
					<select name="project_id">
						<?php foreach(getprojects() as $project) { ?>
						<option value="<?php echo $project['id']?>"<?php echo iif($project['id'] == @$page['project_id'],' selected="selected"')?>><?php echo $project['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('Body')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="body" style="width:100%;height:200px"><?php echo @$page['body']?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l((isset($_REQUEST['edit']) ? 'update' : 'create'))?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Delete Wiki page
elseif(isset($_REQUEST['delete']))
{
	$page = $db->queryfirst("SELECT main FROM ".DBPF."wiki WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	if(!$page['main'])
	{
		$db->query("DELETE FROM ".DBPF."wiki WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
		header("Location: wiki.php?deleted");
	}
}
// List Wiki pages
else
{
	// Get Pages
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPF."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects))
	{
		$info['pages'] = array();
		$fetchpages = $db->query("SELECT * FROM ".DBPF."wiki WHERE project_id='".$info['id']."' ORDER BY title ASC");
		while($page = $db->fetcharray($fetchpages))
			$info['pages'][] = $page;
		
		$projects[] = $info;
	}
	
	head(l('Wiki'),true,'wiki');
	?>
	<h2><?php echo l('Wiki')?></h2>
	<?php
	foreach($projects as $project) { ?>
	<div class="thead"><?php echo $project['name']?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('title')?></th>
				<th></th>
			</tr>
			<?php foreach($project['pages'] as $page) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="wiki.php?edit=<?php echo $page['id']?>"><?php echo $page['title']?></a></td>
				<td align="right">
					<a href="wiki.php?edit=<?php echo $page['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<?php if(!$page['main']) { ?><a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$page['title'])?>')) { window.location = 'wiki.php?delete=<?php echo $page['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a><?php } ?>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($project['pages'])) { ?>
			<tr>
				<td align="center" colspan="3"><?php echo l('no_pages')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	}
	foot();
}
?>