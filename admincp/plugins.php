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

if(!isset($_REQUEST['action'])) {
	$hiddenfiles = array('.','..','.svn');
	
	// Get Active Plugins
	$plugins = array('enabled'=>array(),'disabled'=>array(),'loaded'=>array());
	$fetchplugins = $db->query("SELECT * FROM ".DBPREFIX."plugins");
	while($info = $db->fetcharray($fetchplugins)) {
		$plugins['loaded'][] = $info['file'];
	}
	
	// Get Plugin Files
	$dir = scandir(TRAQPATH.'plugins');
	foreach($dir as $file) {
		if(!in_array($file,$hiddenfiles)) {
			$data = implode('',file(TRAQPATH.'/plugins/'.$file));
			preg_match('| Name: (.*)$|mi',$data,$name); // Plugin Name
			preg_match('| Author:(.*)$|mi',$data, $author); // Plugin Author
			preg_match('| URL:(.*)$|mi',$data, $url); // Plugin Website URL
			preg_match('| Info:(.*)$|mi',$data, $info); // Plugin Description
			preg_match('| Version:(.*)$|mi',$data, $version); // Plugin Author
			$plugin = array(
							'name' => trim($name[1]),
							'author' => $author[1],
							'info' => $info[1],
							'url' => $url[1],
							'version' => $version[1],
							'file' => $file
							);
			if(in_array($file,$plugins['loaded'])) {
				$plugins['enabled'][] = $plugin;
			} else {
				$plugins['disabled'][] = $plugin;
			}
		}
	}
	
	adminheader('Plugins');
	?>
	<div id="content">
		<div class="content-group">
			<div class="content-title">Enabled Plugins</div>
			<table width="100%" class="pluginlist" cellspacing="0" cellpadding="4">
				<thead>
					<th class="name">Name</th>
					<th class="author" width="150">Author</th>
					<th class="version" width="150">Version</th>
					<th class="actions" width="100">Actions</th>
				</thead>
				<? foreach($plugins['enabled'] as $plugin) { ?>
				<tr>
					<td>
						<? if($plugin['url']) { ?>
							<a href="<?=$plugin['url']?>"><?=$plugin['name']?></a>
						<? } else { ?>
							<?=$plugin['name']?>
						<? } ?><br />
						<?=$plugin['info']?>
					</td>
					<td><?=$plugin['author']?></td>
					<td><?=$plugin['version']?></td>
					<td><a href="plugins.php?action=disable&file=<?=$plugin['file']?>">Disable</a></td>
				</tr>
				<? } ?>
			</table>
		</div>
		<br />
		<div class="content-group">
			<div class="content-title">Disabled Plugins</div>
			<table width="100%" class="pluginlist" cellspacing="0" cellpadding="4">
				<thead>
					<th class="name">Name</th>
					<th class="author" width="150">Author</th>
					<th class="version" width="150">Version</th>
					<th class="actions" width="100">Actions</th>
				</thead>
				<? foreach($plugins['disabled'] as $plugin) { ?>
				<tr>
					<td>
						<? if($plugin['url']) { ?>
							<a href="<?=$plugin['url']?>"><?=$plugin['name']?></a>
						<? } else { ?>
							<?=$plugin['name']?>
						<? } ?><br />
						<?=$plugin['info']?>
					</td>
					<td><?=$plugin['author']?></td>
					<td><?=$plugin['version']?></td>
					<td><a href="plugins.php?action=enable&file=<?=$plugin['file']?>">Enable</a></td>
				</tr>
				<? } ?>
			</table>
		</div>
	</div>
	<?
	adminfooter();
} elseif($_REQUEST['action'] == "enable") {
	$db->query("INSERT INTO ".DBPREFIX."plugins VALUES('".$db->escapestring($_REQUEST['file'])."')");
	header("Location: plugins.php");
} elseif($_REQUEST['action'] == "disable") {
	$db->query("DELETE FROM ".DBPREFIX."plugins WHERE file='".$db->escapestring($_REQUEST['file'])."'");
	header("Location: plugins.php");
}
?>