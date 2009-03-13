<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

/**
 * Get Users
 * Used to get all the users from the DB.
 */
function getusers() {
	global $db;
	$users = array();
	$fetchusers = $db->query("SELECT id,username,email FROM ".DBPREFIX."users ORDER BY id ASC");
	while($info = $db->fetcharray($fetchusers)) {
		$users[] = $info;
	}
	unset($fetchusers,$info);
	return $users;
}

/**
 * Get Projects
 * Used to get all the projects from the DB.
 */
function getprojects() {
	global $db;
	$projects = array();
	$fetchprojects = $db->query("SELECT * FROM ".DBPREFIX."projects ORDER BY name ASC");
	while($info = $db->fetcharray($fetchprojects)) {
		$projects[] = $info;
	}
	unset($fetchprojects,$info);
	return $projects;
}

/**
 * Get Groups
 * Used to get all the usergroups from the DB.
 */
function getgroups() {
	global $db;
	$groups = array();
	$fetchgroups = $db->query("SELECT * FROM ".DBPREFIX."usergroups ORDER BY name ASC");
	while($info = $db->fetcharray($fetchgroups)) {
		$groups[] = $info;
	}
	return $groups;
}

/**
 * Admin Header
 * Used to display the admincp header.
 */
function adminheader($title='') {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<title><?=($title != '' ? $title.' - ' : '')?>Traq AdminCP</title>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<strong>Traq AdminCP</strong>
	</div>
	<div id="middle">
		<div id="sidebar">
			<div class="sidebar-group">
				<div class="sidebar-title">Traq</div>
				<ul class="sidebar-options">
					<li><a href="index.php">Summary</a></li>
					<li><a href="../">View Site</a></li>
					<li><a href="settings.php">Settings</a></li>
					<li><a href="plugins.php">Plugins</a></li>
				</ul>
			</div>
			<div class="sidebar-group">
				<div class="sidebar-title">Projects</div>
				<ul class="sidebar-options">
					<li><a href="projects.php?action=new">New Project</a></li>
					<li><a href="projects.php">Manage Projects</a></li>
					<li><hr /></li>
					<li><a href="milestones.php?action=new">New Milestone</a></li>
					<li><a href="milestones.php">Manage Milestones</a></li>
					<li><hr /></li>
					<li><a href="versions.php?action=new">New Version</a></li>
					<li><a href="versions.php">Manage Versions</a></li>
					<li><hr /></li>
					<li><a href="components.php?action=new">New Component</a></li>
					<li><a href="components.php">Manage Components</a></li>
				</ul>
			</div>
			<div class="sidebar-group">
				<div class="sidebar-title">Tickets</div>
				<ul class="sidebar-options">
					<li><a href="types.php">Manage Types</a></li>
					<li><a href="priorities.php">Manage Priorities</a></li>
					<li><a href="severities.php">Manage Severities</a></li>
					<li><a href="statustypes.php">Manage Status Types</a></li>
				</ul>
			</div>
			<div class="sidebar-group">
				<div class="sidebar-title">Users</div>
				<ul class="sidebar-options">
					<li><a href="users.php">Manage Users</a></li>
					<li><hr /></li>
					<li><a href="groups.php?action=new">New Usergroup</a></li>
					<li><a href="groups.php">Manage Groups</a></li>
				</ul>
			</div>
		</div>
<?
}

/**
 * Admin Footer
 * Used to display the admincp footer.
 */
function adminfooter() {
?>
	<div class="clear"></div>
	</div>
	<div id="footer">
		Powered by Traq <?=TRAQVER?>,<br />
		Copyright &copy;<?=date("Y")?> Rainbird Studios
	</div>
</div>
</body>
</html>
<?
}
?>