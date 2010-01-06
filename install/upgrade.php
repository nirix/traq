<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

require("../include/version.php");
require("../include/config.php");
require("../include/database.class.php");
$db = new Database;
$db->connect($config->db->host,$config->db->user,$config->db->pass);
$db->selectdb($config->db->name);
$db->prefix = $config->db->prefix;
define("DBPREFIX",$db->prefix);
require("common.php");

// Get settings
$settings = (object) array();
$fetchsettings = $db->query("SELECT setting,value FROM ".$db->prefix."settings");
while($info = $db->fetcharray($fetchsettings)) {
	$settings->$info['setting'] = $info['value'];
}
unset($fetchsettings,$info);

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
	There is a database upgrade available, click next to continue.<br />
	<strong>It is highly recommended that you backup your database before each upgrade.</strong>
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
		CREATE TABLE `traq_attachments` (
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
	if($settings->dbversion < 4) {
		$sql = "
		ALTER TABLE `traq_tickethistory` ADD `comment` LONGTEXT NOT NULL AFTER `changes` ;
		ALTER TABLE `traq_usergroups` ADD `updatetickets` SMALLINT NOT NULL AFTER `isadmin` ;
		UPDATE `traq_usergroups` SET `updatetickets` = '1' WHERE `traq_usergroups`.`id` =1 LIMIT 1 ;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
		$fetchcomments = $db->query("SELECT * FROM ".$config->db->prefix."ticketcomments");
		while($info = $db->fetcharray($fetchcomments)) {
			$db->query("INSERT INTO ".$config->db->prefix."tickethistory VALUES(0,".$info['timestamp'].",".$info['authorid'].",".$info['ticketid'].",'','".$db->escapestring(stripslashes($info['body']))."')");
		}
		$db->query("DROP TABLE IF EXISTS `traq_ticketcomments`;");
	}
	if($settings->dbversion < 5) {
		$sql = "INSERT INTO `traq_usergroups` ( `id` , `name` , `isadmin` , `updatetickets` )
VALUES (
NULL , 'Guests', '0', '0'
);";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 6) {
		$count = 0;
		$fetchrows = $db->query("SELECT * FROM ".$config->db->prefix."timeline");
		while($info = $db->fetcharray($fetchrows)) {
			$count++;
			$db->query("UPDATE ".$config->db->prefix."timeline SET id=$count WHERE type='".$info['type']."' AND data='".$info['data']."' AND timestamp='".$info['timestamp']."' AND date='".$info['date']."' AND userid='".$info['userid']."' AND projectid='".$info['projectid']."' LIMIT 1");
		}
		$sql = "ALTER TABLE `traq_timeline` ADD PRIMARY KEY ( `id` );
ALTER TABLE `traq_timeline` CHANGE `id` `id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 7) {
		$sql = "CREATE TABLE `traq_plugins` (
  `file` varchar(255) NOT NULL,
  PRIMARY KEY  (`file`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 8) {
		$sql = "ALTER TABLE `traq_users` CHANGE `uid` `id` BIGINT( 255 ) NOT NULL AUTO_INCREMENT;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 9) {
		$sql = "ALTER TABLE `traq_attachments` ADD `ownername` VARCHAR( 255 ) NOT NULL AFTER `ownerid`;
ALTER TABLE `traq_tickethistory` ADD `username` VARCHAR( 255 ) NOT NULL AFTER `userid`;
ALTER TABLE `traq_tickets` ADD `ownername` VARCHAR( 255 ) NOT NULL AFTER `ownerid`;
ALTER TABLE `traq_timeline` ADD `username` VARCHAR( 255 ) NOT NULL AFTER `userid`;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
		$fetchrows = $db->query("SELECT * FROM ".DBPREFIX."timeline");
		while($info = $db->fetcharray($fetchrows)) {
			$owner = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."users WHERE id='".$info['userid']."' LIMIT 1"));
			$db->query("UPDATE ".DBPREFIX."timeline SET username='".$owner['username']."' WHERE id='".$info['id']."' LIMIT 1");
		}
		
		$fetchrows = $db->query("SELECT * FROM ".DBPREFIX."tickets");
		while($info = $db->fetcharray($fetchrows)) {
			$owner = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."users WHERE id='".$info['ownerid']."' LIMIT 1"));
			$db->query("UPDATE ".DBPREFIX."tickets SET ownername='".$owner['username']."' WHERE id='".$info['id']."' LIMIT 1");
		}
		
		$fetchrows = $db->query("SELECT * FROM ".DBPREFIX."tickethistory");
		while($info = $db->fetcharray($fetchrows)) {
			$owner = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."users WHERE id='".$info['userid']."' LIMIT 1"));
			$db->query("UPDATE ".DBPREFIX."tickethistory SET username='".$owner['username']."' WHERE id='".$info['id']."' LIMIT 1");
		}
		
		$fetchrows = $db->query("SELECT * FROM ".DBPREFIX."attachments");
		while($info = $db->fetcharray($fetchrows)) {
			$owner = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."users WHERE id='".$info['ownerid']."' LIMIT 1"));
			$db->query("UPDATE ".DBPREFIX."attachments SET ownername='".$owner['username']."' WHERE id='".$info['id']."' LIMIT 1");
		}
	}
	if($settings->dbversion < 10) {
		$sql = "ALTER TABLE `traq_usergroups` ADD `createtickets` SMALLINT( 6 ) NOT NULL AFTER `isadmin`;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 11) {
		$sql = "CREATE TABLE `traq_severities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `traq_severities` (`id`, `name`) VALUES 
(1, 'Blocker'),
(2, 'Critical'),
(3, 'Major'),
(4, 'Normal'),
(5, 'Minor'),
(6, 'Trivial');

CREATE TABLE `traq_priorities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `traq_priorities` (`id`, `name`) VALUES 
(1, 'Lowest'),
(2, 'Low'),
(3, 'Normal'),
(4, 'High'),
(5, 'Highest');

CREATE TABLE `traq_statustypes` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `traq_statustypes` (`id`, `name`) VALUES 
(-3, 'Fixed'),
(-2, 'Invalid'),
(-1, 'Completed'),
(0, 'Closed'),
(1, 'New'),
(2, 'Accepted'),
(3, 'Reopened'),
(4, 'Started');

CREATE TABLE `traq_types` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `traq_types` (`id`, `name`) VALUES 
(1, 'Defect'),
(2, 'Enhancement'),
(3, 'Feature Request'),
(4, 'Task');";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
		$db->query("UPDATE ".$config->db->prefix."usergroups SET createtickets=1 WHERE id != 3");
	}
	if($settings->dbversion < 12) {
		$sql = "ALTER TABLE `traq_projects` ADD `sourcelocation` LONGTEXT NOT NULL AFTER `currenttid`;";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 13) {
		$sql = "INSERT INTO `traq_settings` (`setting`,`value`) VALUES ('uritype', '1');";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 14) {
		$sql = "INSERT INTO `traq_settings` VALUES ('akismetkey', '');";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 15) {
		$sql = "ALTER TABLE  `traq_milestones` CHANGE  `milestone`  `milestone` VARBINARY( 255 ) NOT NULL";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 16) {
		$sql = "INSERT INTO  `traq_settings` (`setting`,`value`) VALUES ('langfile','enus');";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
				$db->query($query);
			}
		}
	}
	if($settings->dbversion < 17) {
		$sql = "ALTER TABLE `traq_milestones` CHANGE `milestone` `milestone` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ";
		$sql = str_replace('traq_',$config->db->prefix,$sql);
		$queries = explode(';',$sql);
		foreach($queries as $query) {
			if(!empty($query)) {
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