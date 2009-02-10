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

$dbversion = 3;
if(!isset($settings->dbversion)) {
	$upgradeavailable = 1;
} else if($settings->dbversion < $dbversion) {
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
		$sql = "
			INSERT INTO `traq_settings` ( `setting` , `value` )
			VALUES (
			'dbversion', '1'
			);
			DROP TABLE IF EXISTS `traq_timeline`;
			CREATE TABLE `traq_timeline` (
			`id` BIGINT NOT NULL ,
			`type` BIGINT NOT NULL ,
			`data` LONGTEXT NOT NULL ,
			`timestamp` BIGINT NOT NULL ,
			`date` DATE NOT NULL ,
			`userid` BIGINT NOT NULL ,
			`projectid` BIGINT NOT NULL
			) ENGINE = innodb;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query) && $query != ' ') {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 2) {
		$sql = "
			INSERT INTO `traq_settings` ( `setting` , `value` )
			VALUES (
			'theme', 'default'
			);";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query) && $query != ' ') {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 3) {
		$sql = "
		CREATE TABLE `t_attachments` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `name` varchar(255) NOT NULL,
		  `contents` longtext NOT NULL,
		  `type` varchar(255) NOT NULL,
		  `timestamp` bigint(20) NOT NULL,
		  `ownerid` bigint(20) NOT NULL,
		  `ticketid` bigint(20) NOT NULL,
		  `projectid` bigint(20) NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query) && $query != ' ') {
				$db->query($query);
			}
		}
	}
	?>
	Database upgrade complete.
	<?
	foot();
	$db->query("UPDATE ".$db->prefix."settings SET value=".$dbversion." WHERE setting='dbversion' LIMIT 1");
}
?>