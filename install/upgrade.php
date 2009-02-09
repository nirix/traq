<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

require("../include/config.php");
require("../include/origin/origin.php");
$origin = new Origin;
$origin->load("database",'db');
$origin->db->connect($config->db->host,$config->db->user,$config->db->pass);
$origin->db->selectdb($config->db->name);
$origin->db->prefix = $config->db->prefix;
$db =& $origin->db;
require("common.php");

// Get settings
$settings = (object) array();
$fetchsettings = $origin->db->query("SELECT setting,value FROM ".$db->prefix."settings");
while($info = $origin->db->fetcharray($fetchsettings)) {
	$settings->$info['setting'] = $info['value'];
}
unset($fetchsettings,$info);

if(!isset($settings->dbversion)) {
	$upgradeavailable = 1;
}

if(!isset($_POST['step'])) {
	head('Upgrade');
	if($upgradeavailable) {
	?>
	<form action="upgrade.php" method="post">
	<input type="hidden" name="step" value="1" />
	There is a database upgrade available, click next to continue.
	<div id="buttons">
		<input type="submit" value="Next" />
	</div>
	</form>
	<?
	} else {
	?>
	Your database appears to be up-to-date.
	<?
	}
	foot();
} elseif($_POST['step'] = 1) {
	head('Upgrade');
	if(!isset($settings->dbversion)) {
		$upgradesql = file_get_contents('sql/upgrade1.sql');
		$upgradesql = str_replace('traq_',$config->db->prefix,$upgradesql);
		$queries = explode(';',$upgradesql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
		?>
		Database upgrade complete.
		<?
	}
	foot();
}
?>