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

// Install Plugin
if(isset($_REQUEST['install']))
{
	if(isset($_POST['install']))
	{
		if(empty($_FILES['pluginfile']['name'])) { exit; }
		
		$plugin = simplexml_load_file($_FILES['pluginfile']['tmp_name']);
		// Insert plugin
		$db->query("INSERT INTO ".DBPF."plugins VALUES(
			0,
			'".$db->res((string)$plugin->info->name)."',
			'".$db->res((string)$plugin->info->author)."',
			'".$db->res((string)$plugin->info->website)."',
			'".$db->res((string)$plugin->info->version)."',
			'1',
			'".$db->res((string)$plugin->sql->install)."',
			'".$db->res((string)$plugin->sql->uninstall)."'
		)");
		$pluginid = $db->insertid();
		
		// Run the install SQL
		if($plugin->sql->install != '')
		{
			$queries = explode(';',$plugin->sql->install);
			foreach($queries as $query)
				if($query != '')
					$db->query(str_replace('traq_',DBPF,$query));
		}
		
		// Add the hooks
		foreach($plugin->hooks->hook as $hook)
		{
			$db->query("INSERT INTO ".DBPF."plugin_code VALUES(
				0,
				'".$pluginid."',
				'".$db->res((string)$hook['title'])."',
				'".$db->res((string)$hook['hook'])."',
				'".$db->res((string)$hook->code)."',
				'".$db->res((integer)$hook['execorder'])."',
				'1'
			)");
		}
		
		header("Location: plugins.php");
	}
	
	head(l('install_plugin'),true,'plugins');
	?>
	<form action="plugins.php?install" method="post" enctype="multipart/form-data">
	<input type="hidden" name="install" value="1" />
	<div class="thead">Install Plugin</div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('plugin_file')?></td>
			</tr>
			<tr>
				<td><?php echo l('plugin_file_description')?></td>
				<td align="right"><input type="file" name="pluginfile" />
			</tr>
			<tr>
				<td colspan="2" class="tfoot"><div align="center"><input type="submit" value="<?php echo l('install')?>" /></div></td>
			</tr>
		</table>
	</div>
	</form>
	<?php
	foot();
}
// Create new Plugin
elseif(isset($_REQUEST['create']))
{
	if(isset($_POST['name']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_plugin_name_blank');
		if(empty($_POST['author']))
			$errors['author'] = l('error_plugin_author_blank');
		if(empty($_POST['version']))
			$errors['version'] = l('error_plugin_version_blank');
			
		if(!count($errors))
		{
			$db->query("INSERT INTO ".DBPF."plugins (name,author,website,version,enabled,install_sql,uninstall_sql)
			VALUES(
				'".$db->res($_POST['name'])."',
				'".$db->res($_POST['author'])."',
				'".$db->res($_POST['website'])."',
				'".$db->res($_POST['version'])."',
				'1',
				'".$db->res($_POST['install_sql'])."',
				'".$db->res($_POST['uninstall_sql'])."'
			)");
			
			// Run the install SQL
			if($_POST['install_sql'] != '')
			{
				$queries = explode(';',$_POST['install_sql']);
				foreach($queries as $query)
					if($query != '')
						$db->query($query);
			}
			
			header("Location: plugins.php?created");
		}
	}
	
	head(l('create_plugin'),true,'plugins');
	?>
	<?php if(count(@$errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="plugins.php?create" method="post">
	<div class="thead"><?php echo l('create_plugin')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('plugin_name')?></td>
			</tr>
			<tr>
				<td><?php echo l('plugin_name_description')?></td>
				<td align="right"><input type="text" name="name" value="<?php echo @$_POST['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_author')?></td>
			</tr>
			<tr>
				<td><?php echo l('plugin_author_description')?></td>
				<td align="right"><input type="text" name="author" value="<?php echo @$_POST['author']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_website')?></td>
			</tr>
			<tr>
				<td><?php echo l('plugin_website_description')?></td>
				<td align="right"><input type="text" name="website" value="<?php echo @$_POST['website']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_version')?></td>
			</tr>
			<tr>
				<td><?php echo l('plugin_version_description')?></td>
				<td align="right"><input type="text" name="version" value="<?php echo @$_POST['version']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_install_sql')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="install_sql" style="width:100%;height:150px"><?php echo @$_POST['install_sql']?></textarea></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_uninstall_sql')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="uninstall_sql" style="width:100%;height:150px"><?php echo @$_POST['uninstall_sql']?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('create')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Edit Plugin
elseif(isset($_REQUEST['edit']) && isset($_REQUEST['plugin']))
{
	// Update Plugin
	if(isset($_POST['name']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['name']))
			$errors['name'] = l('error_plugin_name_blank');
		if(empty($_POST['author']))
			$errors['author'] = l('error_plugin_author_blank');
		if(empty($_POST['version']))
			$errors['version'] = l('error_plugin_version_blank');
		
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."plugins SET
			name='".$db->res($_POST['name'])."',
			author='".$db->res($_POST['author'])."',
			website='".$db->res($_POST['website'])."',
			version='".$db->res($_POST['version'])."',
			install_sql='".$db->res($_POST['install_sql'])."',
			uninstall_sql='".$db->res($_POST['uninstall_sql'])."'
			WHERE id='".$db->res($_REQUEST['plugin'])."' LIMIT 1");
			
			header("Location: plugins.php?updated");
		}
	}
	
	// Fetch Plugin info
	$plugin = $db->queryfirst("SELECT * FROM ".DBPF."plugins WHERE id='".$db->res($_REQUEST['plugin'])."' LIMIT 1");
	
	head(l('edit_plugin'),true,'plugins');
	?>
	<?php if(count(@$errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="plugins.php?edit&amp;plugin=<?php echo $_REQUEST['plugin']?>" method="post">
	<div class="thead"><?php echo l('edit_plugin')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('plugin_name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('plugin_name_description')?></td>
				<td align="right"><input type="text" name="name" value="<?php echo $plugin['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_author')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('plugin_author_description')?></td>
				<td align="right"><input type="text" name="author" value="<?php echo $plugin['author']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_website')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('plugin_website_description')?></td>
				<td align="right"><input type="text" name="website" value="<?php echo $plugin['website']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_version')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('plugin_version_description')?></td>
				<td align="right"><input type="text" name="version" value="<?php echo $plugin['version']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_install_sql')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="install_sql" style="width:100%;height:150px"><?php echo stripslashes($plugin['install_sql'])?></textarea></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('plugin_uninstall_sql')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="uninstall_sql" style="width:100%;height:150px"><?php echo stripslashes($plugin['uninstall_sql'])?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Plugin Hook listing
elseif(isset($_REQUEST['hooks']))
{
	// Fetch Plugin info
	$plugin = $db->queryfirst("SELECT name FROM ".DBPF."plugins WHERE id='".$_REQUEST['plugin']."' LIMIT 1");
	
	// Fetch Plugin hooks
	$hooks = array();
	$fetchhooks = $db->query("SELECT id,title,hook,code FROM ".DBPF."plugin_code WHERE plugin_id='".$db->res($_REQUEST['plugin'])."' ORDER BY title ASC");
	while($info = $db->fetcharray($fetchhooks))
	{
		$hooks[] = $info;
	}
	
	head(l('plugin_hooks_for_x',$plugin['name']),true,'plugins');
	?>
	<div class="thead"><?php echo l('plugin_hooks_for_x',$plugin['name'])?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('title')?></th>
				<th width="200" align="left"><?php echo l('hook')?></th>
				<th></th>
			</tr>
			<?php foreach($hooks as $hook) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="plugins.php?edithook&amp;hook=<?php echo $hook['id']?>"><?php echo $hook['title']?></a></td>
				<td><?php echo $hook['hook']?></td>
				<td align="right">
					<a href="plugins.php?edithook&amp;hook=<?php echo $hook['id']?>"><img src="images/plugin_edit.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('delete_plugin_hook_confirm')?>')) { window.location='plugins.php?removehook&amp;hook=<?php echo $hook['id']?>'; } return false;"><img src="images/plugin_delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	foot();
}
// New Hook
elseif(isset($_REQUEST['newhook']))
{
	// Create Hook
	if(isset($_POST['title']))
	{
		// Check for errors...
		$errors = array();
		if(empty($_POST['plugin_id']))
			$errors['plugin_id'] = l('error_hook_plugin_blank');
		if(empty($_POST['title']))
			$errors['title'] = l('error_title_blank');
		if(empty($_POST['hook']))
			$errors['hook'] = l('error_select_a_hook');
		
		if(!count($errors))
		{
			$db->query("INSERT INTO ".DBPF."plugin_code (plugin_id,title,hook,code,execorder,enabled)
			VALUES(
			'".$db->res($_POST['plugin_id'])."',
			'".$db->res($_POST['title'])."',
			'".$db->res($_POST['hook'])."',
			'".$db->res($_POST['code'])."',
			'".$db->res($_POST['execorder'])."',
			'1'
			)");
			
			header("Location: plugins.php?created");
		}
	}
	
	// Fetch plugins
	$plugins = array();
	$fetchplugins = $db->query("SELECT id,name FROM ".DBPF."plugins ORDER BY name ASC");
	while($info = $db->fetcharray($fetchplugins))
	{
		$plugins[] = $info;
	}
	
	head(l('new_hook'),true,'plugins');
	?>
	<?php if(count(@$errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="plugins.php?newhook" method="post">
	<div class="thead"><?php echo l('new_hook')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('plugin')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('hook_plugin_description')?></td>
				<td align="right">
					<select name="plugin_id">
						<?php foreach($plugins as $plugin) { ?>
						<option value="<?php echo $plugin['id']?>"<?php echo iif($plugin['id']==@$_POST['plugin_id'],' selected="selected"')?>><?php echo $plugin['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('title')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('hook_title_description')?></td>
				<td align="right"><input type="text" name="title" value="<?php echo @$_POST['title']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('hook')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('hook_description')?></td>
				<td align="right">
					<select name="hook">
						<option value=""><?php echo l('select_hook')?></option>
						<?php foreach($hook_locations as $key => $value) { ?>
						<?php if(is_array($value)) { ?>
							<optgroup label="<?php echo l($key)?>">
								<?php foreach($value as $hookname) { ?>
								<option value="<?php echo $hookname?>"<?php echo iif($hookname==@$_POST['hook'],' selected="selected"')?>><?php echo $hookname?></option>
								<?php } ?>
							</optgroup>
						<?php } else { ?>
						<option value="<?php echo $value?>"<?php echo iif($value==@$_POST['hook'],' selected="selected"')?>><?php echo $value?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('execution_order')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('hook_execution_order_description')?></td>
				<td align="right"><input type="text" name="execorder" value="<?php echo iif(@$_POST['execorder'],@$_POST['execorder'],0)?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('code')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="code" style="width:100%;height:150px"><?php echo @$_POST['code']?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('create')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Edit Hook
elseif(isset($_REQUEST['edithook']))
{
	// Save Hook
	if(isset($_POST['title']))
	{	
		// Check for errors...
		$errors = array();
		if(empty($_POST['plugin_id']))
			$errors['plugin_id'] = l('error_hook_plugin_blank');
		if(empty($_POST['title']))
			$errors['title'] = l('error_title_blank');
		if(empty($_POST['hook']))
			$errors['hook'] = l('error_select_a_hook');
		
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."plugin_code SET
			plugin_id='".$db->res($_POST['plugin_id'])."',
			title='".$db->res($_POST['title'])."',
			hook='".$db->res($_POST['hook'])."',
			execorder='".$db->res($_POST['execorder'])."',
			code='".$db->res($_POST['code'])."'
			WHERE id='".$db->res($_POST['hook_id'])."' LIMIT 1
			");
			
			header("Location: plugins.php?edithook&hook=".$_POST['hook_id']);
		}
	}
	
	// Fetch Hook info
	$hook = $db->queryfirst("SELECT * FROM ".DBPF."plugin_code WHERE id='".$db->res($_REQUEST['hook'])."' LIMIT 1");
	
	// Fetch plugins
	$plugins = array();
	$fetchplugins = $db->query("SELECT id,name FROM ".DBPF."plugins ORDER BY name ASC");
	while($info = $db->fetcharray($fetchplugins))
	{
		$plugins[] = $info;
	}
	
	head(l('edit_hook'),true,'plugins');
	?>
	<?php if(count(@$errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="plugins.php?edithook" method="post">
	<input type="hidden" name="hook_id" value="<?php echo $hook['id']?>" />
	<div class="thead"><?php echo l('edit_hook')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('plugin')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('hook_plugin_description')?></td>
				<td align="right">
					<select name="plugin_id">
						<?php foreach($plugins as $plugin) { ?>
						<option value="<?php echo $plugin['id']?>"<?php echo iif($plugin['id']==$hook['plugin_id'],' selected="selected"')?>><?php echo $plugin['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('title')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('hook_title_description')?></td>
				<td align="right"><input type="text" name="title" value="<?php echo $hook['title']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('hook')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('hook_description')?></td>
				<td align="right">
					<select name="hook">
						<option value=""><?php echo l('select_hook')?></option>
						<?php foreach($hook_locations as $key => $value) { ?>
						<?php if(is_array($value)) { ?>
							<optgroup label="<?php echo l($key)?>">
								<?php foreach($value as $hookname) { ?>
								<option value="<?php echo $hookname?>"<?php echo iif($hookname==$hook['hook'],' selected="selected"')?>><?php echo $hookname?></option>
								<?php } ?>
							</optgroup>
						<?php } else { ?>
						<option value="<?php echo $value?>"<?php echo iif($value==$hook['hook'],' selected="selected"')?>><?php echo $value?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('execution_order')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('hook_execution_order_description')?></td>
				<td align="right"><input type="text" name="execorder" value="<?php echo $hook['execorder']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('code')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td colspan="2"><textarea name="code" style="width:100%;height:150px"><?php echo htmlspecialchars($hook['code'])?></textarea></td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Export Plugin
elseif(isset($_REQUEST['export']))
{
	// Fetch Plugin info
	$plugin = $db->queryfirst("SELECT * FROM ".DBPF."plugins WHERE id='".$db->res($_REQUEST['plugin'])."' LIMIT 1");
	
	// Fetch Hooks
	$hooks = array();
	$fetchhooks = $db->query("SELECT * FROM ".DBPF."plugin_code WHERE plugin_id='".$db->res($_REQUEST['plugin'])."' ORDER BY hook ASC");
	while($info = $db->fetcharray($fetchhooks))
	{
		$hooks[] = $info;
	}
	
	header('Content-type: application/xml');
	echo '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>'.PHP_EOL;
	echo '	<plugin>'.PHP_EOL;
	echo '		<info>'.PHP_EOL;
	echo '		<name>'.$plugin['name'].'</name>'.PHP_EOL;
	echo '		<author>'.$plugin['author'].'</author>'.PHP_EOL;
	echo '		<website>'.$plugin['website'].'</website>'.PHP_EOL;
	echo '		<version>'.$plugin['version'].'</version>'.PHP_EOL;
	echo '	</info>'.PHP_EOL;
	echo '	<sql>'.PHP_EOL;
	echo '		<install>'.str_replace(DBPF,'traq_',$plugin['install_sql']).'</install>'.PHP_EOL;
	echo '		<uninstall>'.str_replace(DBPF,'traq_',$plugin['uninstall_sql']).'</uninstall>'.PHP_EOL;
	echo '	</sql>'.PHP_EOL;
	echo '	<hooks>'.PHP_EOL;
	foreach($hooks as $hook) {
		echo '		<hook title="'.$hook['title'].'" hook="'.$hook['hook'].'" execorder="'.$hook['execorder'].'">'.PHP_EOL;
		echo '			<code><![CDATA['.$hook['code'].']]></code>'.PHP_EOL;
		echo '		</hook>'.PHP_EOL;
	}
	echo '	</hooks>'.PHP_EOL;
	echo '</plugin>'.PHP_EOL;
}
// Delete Hook
elseif(isset($_REQUEST['removehook']))
{
	$db->query("DELETE FROM ".DBPF."plugin_code WHERE id='".$db->res($_REQUEST['hook'])."' LIMIT 1");
	header("Location: plugins.php");
}
// Uninstall Plugin
elseif(isset($_REQUEST['remove']))
{
	// Fetch Plugin uninstall sql
	$plugin = $db->queryfirst("SELECT uninstall_sql FROM ".DBPF."plugins WHERE id='".$db->res($_REQUEST['plugin'])."' LIMIT 1");
	$queries = explode(';',$plugin['uninstall_sql']);
	
	// Run queries
	foreach($queries as $query)
		if($query != '')
			$db->query($query);
	
	// Remove from the Database
	$db->query("DELETE FROM ".DBPF."plugins WHERE id='".$db->res($_REQUEST['plugin'])."' LIMIT 1");
	$db->query("DELETE FROM ".DBPF."plugin_code WHERE plugin_id='".$db->res($_REQUEST['plugin'])."'");
	
	header("Location: plugins.php");
}
// Disable Plugin
elseif(isset($_REQUEST['disable']))
{
	$db->query("UPDATE ".DBPF."plugins SET enabled='0' WHERE id='".$db->res($_REQUEST['plugin'])."' LIMIT 1");
	$db->query("UPDATE ".DBPF."plugin_code SET enabled='0' WHERE plugin_id='".$db->res($_REQUEST['plugin'])."'");
	header("Location: plugins.php");
}
// Enable Plugin
elseif(isset($_REQUEST['enable']))
{
	$db->query("UPDATE ".DBPF."plugins SET enabled='1' WHERE id='".$db->res($_REQUEST['plugin'])."' LIMIT 1");
	$db->query("UPDATE ".DBPF."plugin_code SET enabled='1' WHERE plugin_id='".$db->res($_REQUEST['plugin'])."'");
	header("Location: plugins.php");
}
// Plugin listing
else
{
	// Fetch plugins
	$plugins = array('active'=>array(),'disabled'=>array());
	$fetchplugins = $db->query("SELECT * FROM ".DBPF."plugins ORDER BY name ASC");
	while($info = $db->fetcharray($fetchplugins))
	{
		if($info['enabled'])
			$plugins['active'][] = $info;
		else
			$plugins['disabled'][] = $info;
	}
	
	head(l('plugins'),true,'plugins');
	?>
	<div class="thead"><?php echo l('active_plugins')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('plugin')?></th>
				<th width="200"><?php echo l('author')?></th>
				<th width="50"><?php echo l('version')?></th>
				<th></th>
			</tr>
			<?php foreach($plugins['active'] as $plugin) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="plugins.php?edit&amp;plugin=<?php echo $plugin['id']?>"><?php echo $plugin['name']?></a></td>
				<td align="center"><?php echo $plugin['author']?></td>
				<td align="center"><?php echo $plugin['version']?></td>
				<td align="right">
					<a href="plugins.php?edit&amp;plugin=<?php echo $plugin['id']?>"><img src="images/plugin_edit.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="plugins.php?disable&amp;plugin=<?php echo $plugin['id']?>"><img src="images/plugin_disabled.png" alt="<?php echo l('disable')?>" title="<?php echo l('disable')?>" /></a>
					<a href="plugins.php?hooks&amp;plugin=<?php echo $plugin['id']?>"><img src="images/plugin_link.png" alt="<?php echo l('hooks')?>" title="<?php echo l('hooks')?>" /></a>
					<a href="plugins.php?export&amp;plugin=<?php echo $plugin['id']?>"><img src="images/package_go.png" alt="<?php echo l('export')?>" title="<?php echo l('export')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('uninstall_plugin_confirm')?>')) { window.location='plugins.php?remove&amp;plugin=<?php echo $plugin['id']?>'; } return false;"><img src="images/plugin_delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($plugins['active'])) { ?>
			<tr>
				<td align="center" colspan="4"><?php echo l('no_plugins')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<br />
	<div class="thead"><?php echo l('disabled_plugins')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('plugin')?></th>
				<th width="200"><?php echo l('author')?></th>
				<th width="50"><?php echo l('version')?></th>
				<th></th>
			</tr>
			<?php foreach($plugins['disabled'] as $plugin) { ?>
			<tr class="<?php echo altbg()?>">
				<td><a href="plugins.php?edit&amp;plugin=<?php echo $plugin['id']?>"><?php echo $plugin['name']?></a></td>
				<td align="center"><?php echo $plugin['author']?></td>
				<td align="center"><?php echo $plugin['version']?></td>
				<td align="right">
					<a href="plugins.php?edit&amp;plugin=<?php echo $plugin['id']?>"><img src="images/plugin_edit.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="plugins.php?enable&amp;plugin=<?php echo $plugin['id']?>"><img src="images/plugin.png" alt="<?php echo l('enable')?>" title="<?php echo l('enable')?>" /></a>
					<a href="plugins.php?hooks&amp;plugin=<?php echo $plugin['id']?>"><img src="images/plugin_link.png" alt="<?php echo l('hooks')?>" title="<?php echo l('hooks')?>" /></a>
					<a href="plugins.php?export&amp;plugin=<?php echo $plugin['id']?>"><img src="images/package_go.png" alt="<?php echo l('export')?>" title="<?php echo l('export')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('uninstall_plugin_confirm')?>')) { window.location='plugins.php?remove&amp;plugin=<?php echo $plugin['id']?>'; } return false;"><img src="images/plugin_delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
			<?php if(!count($plugins['disabled'])) { ?>
			<tr>
				<td align="center" colspan="4"><?php echo l('no_plugins')?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	foot();
}
?>