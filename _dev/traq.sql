/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table traq_attachments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_attachments`;

CREATE TABLE `traq_attachments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contents` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` bigint(20) NOT NULL,
  `uploaded` int(11) NOT NULL,
  `owner_id` bigint(20) NOT NULL,
  `owner_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table traq_components
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_components`;

CREATE TABLE `traq_components` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default` smallint(6) NOT NULL DEFAULT '0',
  `project_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_components` WRITE;
/*!40000 ALTER TABLE `traq_components` DISABLE KEYS */;

INSERT INTO `traq_components` (`id`, `name`, `default`, `project_id`)
VALUES
	(1,'Core',1,1),
	(2,'Settings',0,1),
	(3,'Core',0,2),
	(4,'Tickets',0,1),
	(5,'Locale',0,1),
	(6,'Plugins',0,1);

/*!40000 ALTER TABLE `traq_components` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_custom_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_custom_fields`;

CREATE TABLE `traq_custom_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` longtext NOT NULL,
  `project_ids` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table traq_milestones
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_milestones`;

CREATE TABLE `traq_milestones` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `codename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `info` longtext COLLATE utf8_unicode_ci NOT NULL,
  `changelog` longtext COLLATE utf8_unicode_ci NOT NULL,
  `due` bigint(20) NOT NULL,
  `is_completed` bigint(20) NOT NULL DEFAULT '0',
  `is_cancelled` bigint(20) NOT NULL DEFAULT '0',
  `is_locked` smallint(6) NOT NULL DEFAULT '0',
  `project_id` bigint(20) NOT NULL,
  `displayorder` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_milestones` WRITE;
/*!40000 ALTER TABLE `traq_milestones` DISABLE KEYS */;

INSERT INTO `traq_milestones` (`id`, `name`, `slug`, `codename`, `info`, `changelog`, `due`, `is_completed`, `is_cancelled`, `is_locked`, `project_id`, `displayorder`)
VALUES
	(1,'1.0','1.0','Awesome','__Test__ *test* [test](/test)\r\n\r\n    <?php\r\n    echo \"Nice\";','',0,0,0,0,1,0),
	(2,'2.0','2.0','','Test','',0,0,0,0,1,0),
	(3,'2.1','2.1','','','',0,0,0,0,1,0),
	(4,'1.0','1.0','','','',0,0,0,0,2,0),
	(5,'2.0','2.0','','','',0,0,0,0,2,0);

/*!40000 ALTER TABLE `traq_milestones` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_permissions`;

CREATE TABLE `traq_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) NOT NULL DEFAULT '0',
  `project_id` bigint(20) NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL DEFAULT '',
  `value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `traq_permissions` WRITE;
/*!40000 ALTER TABLE `traq_permissions` DISABLE KEYS */;

INSERT INTO `traq_permissions` (`id`, `group_id`, `project_id`, `action`, `value`)
VALUES
	(1,0,0,'view',1),
	(2,0,0,'create_wiki_page',0),
	(3,0,0,'edit_wiki_page',0),
	(4,0,0,'delete_wiki_page',0),
	(5,1,0,'create_wiki_page',1),
	(6,1,0,'edit_wiki_page',1),
	(7,1,0,'delete_wiki_page',1);

/*!40000 ALTER TABLE `traq_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_plugins
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_plugins`;

CREATE TABLE `traq_plugins` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_plugins` WRITE;
/*!40000 ALTER TABLE `traq_plugins` DISABLE KEYS */;

INSERT INTO `traq_plugins` (`id`, `file`, `enabled`)
VALUES
	(4,'markdown',1);

/*!40000 ALTER TABLE `traq_plugins` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_priorities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_priorities`;

CREATE TABLE `traq_priorities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_priorities` WRITE;
/*!40000 ALTER TABLE `traq_priorities` DISABLE KEYS */;

INSERT INTO `traq_priorities` (`id`, `name`)
VALUES
	(1,'Lowest'),
	(2,'Low'),
	(3,'Normal'),
	(4,'High'),
	(5,'Highest');

/*!40000 ALTER TABLE `traq_priorities` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_projects
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_projects`;

CREATE TABLE `traq_projects` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `codename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `info` longtext COLLATE utf8_unicode_ci NOT NULL,
  `managers` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `is_private` smallint(6) NOT NULL,
  `next_tid` bigint(20) NOT NULL,
  `displayorder` bigint(20) NOT NULL,
  `private_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_projects` WRITE;
/*!40000 ALTER TABLE `traq_projects` DISABLE KEYS */;

INSERT INTO `traq_projects` (`id`, `name`, `slug`, `codename`, `info`, `managers`, `is_private`, `next_tid`, `displayorder`, `private_key`)
VALUES
	(1,'Test','test','','_This_ is a **project**.\r\n\r\n<strong>WOOT</strong>\r\n\r\n    With _some_ code\r\n    and some more code.\r\n    <strong>YEAH</strong>\r\n\r\n#### Lists!\r\n\r\n- And\r\n- Some\r\n   1. _Pretty_\r\n   2. **Awesome**\r\n- Lists','1',0,1,0,'asd123'),
	(2,'Another test','another-test','','','1',0,1,0,'abc321');

/*!40000 ALTER TABLE `traq_projects` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_repo_changes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_repo_changes`;

CREATE TABLE `traq_repo_changes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `changeset_id` bigint(20) NOT NULL,
  `action` varchar(1) NOT NULL DEFAULT '',
  `path` text NOT NULL,
  `from_path` text,
  `revision` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table traq_repo_changeset_parents
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_repo_changeset_parents`;

CREATE TABLE `traq_repo_changeset_parents` (
  `changeset_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL,
  PRIMARY KEY (`changeset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table traq_repo_changesets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_repo_changesets`;

CREATE TABLE `traq_repo_changesets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `repository_id` bigint(20) NOT NULL,
  `revision` varchar(255) NOT NULL DEFAULT '',
  `commiter` varchar(255) DEFAULT '',
  `committed_on` datetime NOT NULL,
  `comment` text,
  `user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table traq_repositories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_repositories`;

CREATE TABLE `traq_repositories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `extra` text,
  `is_default` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table traq_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_settings`;

CREATE TABLE `traq_settings` (
  `setting` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_settings` WRITE;
/*!40000 ALTER TABLE `traq_settings` DISABLE KEYS */;

INSERT INTO `traq_settings` (`setting`, `value`)
VALUES
	('allow_registration','1'),
	('check_for_update','1'),
	('date_time_format','g:iA d/m/Y'),
	('db_revision','21'),
	('locale','enus'),
	('theme','Default'),
	('timeline_day_format','l, jS F Y'),
	('timeline_time_format','h:iA'),
	('title','Dev');

/*!40000 ALTER TABLE `traq_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_severities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_severities`;

CREATE TABLE `traq_severities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_severities` WRITE;
/*!40000 ALTER TABLE `traq_severities` DISABLE KEYS */;

INSERT INTO `traq_severities` (`id`, `name`)
VALUES
	(1,'Blocker'),
	(2,'Critical'),
	(3,'Major'),
	(4,'Normal'),
	(5,'Minor'),
	(6,'Trivial');

/*!40000 ALTER TABLE `traq_severities` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_subscriptions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_subscriptions`;

CREATE TABLE `traq_subscriptions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `data` longtext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table traq_ticket_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_ticket_history`;

CREATE TABLE `traq_ticket_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` datetime NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `changes` longtext COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table traq_ticket_status
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_ticket_status`;

CREATE TABLE `traq_ticket_status` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL,
  `changelog` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_ticket_status` WRITE;
/*!40000 ALTER TABLE `traq_ticket_status` DISABLE KEYS */;

INSERT INTO `traq_ticket_status` (`id`, `name`, `status`, `changelog`)
VALUES
	(1,'New',1,0),
	(2,'Accepted',1,0),
	(3,'Closed',0,1),
	(4,'Completed',0,1);

/*!40000 ALTER TABLE `traq_ticket_status` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_ticket_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_ticket_types`;

CREATE TABLE `traq_ticket_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bullet` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `changelog` smallint(6) NOT NULL DEFAULT '1',
  `template` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_ticket_types` WRITE;
/*!40000 ALTER TABLE `traq_ticket_types` DISABLE KEYS */;

INSERT INTO `traq_ticket_types` (`id`, `name`, `bullet`, `changelog`, `template`)
VALUES
	(1,'Defect','-',1,''),
	(2,'Feature Request','+',1,''),
	(3,'Task','*',1,'');

/*!40000 ALTER TABLE `traq_ticket_types` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_tickets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_tickets`;

CREATE TABLE `traq_tickets` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) NOT NULL,
  `summary` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `milestone_id` bigint(20) NOT NULL,
  `version_id` bigint(20) NOT NULL,
  `component_id` bigint(20) NOT NULL,
  `type_id` bigint(20) NOT NULL,
  `status_id` bigint(20) NOT NULL DEFAULT '1',
  `priority_id` bigint(20) NOT NULL,
  `severity_id` bigint(20) NOT NULL,
  `assigned_to_id` bigint(20) NOT NULL,
  `is_closed` bigint(20) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_private` smallint(6) NOT NULL DEFAULT '0',
  `extra` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_tickets` WRITE;
/*!40000 ALTER TABLE `traq_tickets` DISABLE KEYS */;

INSERT INTO `traq_tickets` (`id`, `ticket_id`, `summary`, `body`, `user_id`, `project_id`, `milestone_id`, `version_id`, `component_id`, `type_id`, `status_id`, `priority_id`, `severity_id`, `assigned_to_id`, `is_closed`, `created_at`, `updated_at`, `is_private`, `extra`)
VALUES
	(1,1,'Err0rz','This be an error...',1,1,1,1,1,1,1,1,1,1,0,'2012-02-02 12:01:22','2012-02-02 12:01:22',0,'');

/*!40000 ALTER TABLE `traq_tickets` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_timeline
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_timeline`;

CREATE TABLE `traq_timeline` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `owner_id` bigint(20) NOT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_timeline` WRITE;
/*!40000 ALTER TABLE `traq_timeline` DISABLE KEYS */;

INSERT INTO `traq_timeline` (`id`, `project_id`, `owner_id`, `action`, `data`, `user_id`, `timestamp`)
VALUES
	(1,1,1,'ticket_created','1',1,'2012-02-02 12:01:22');

/*!40000 ALTER TABLE `traq_timeline` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_usergroups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_usergroups`;

CREATE TABLE `traq_usergroups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_admin` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_usergroups` WRITE;
/*!40000 ALTER TABLE `traq_usergroups` DISABLE KEYS */;

INSERT INTO `traq_usergroups` (`id`, `name`, `is_admin`)
VALUES
	(1,'Administrators',1),
	(2,'Members',0),
	(3,'Guests',0);

/*!40000 ALTER TABLE `traq_usergroups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_users`;

CREATE TABLE `traq_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_ver` varchar(25) COLLATE utf8_unicode_ci DEFAULT 'crypt',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `group_id` bigint(20) NOT NULL DEFAULT '2',
  `login_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_users` WRITE;
/*!40000 ALTER TABLE `traq_users` DISABLE KEYS */;

INSERT INTO `traq_users` (`id`, `username`, `password`, `name`, `email`, `group_id`, `login_hash`)
VALUES
	(1,'admin','$2a$10$954b2ad8f0c2c913654abubpvDrbWuJPPJ6PO7fhbUxhIxBF/R3by','crypt','admin','test@example.com',1,'abc123');

/*!40000 ALTER TABLE `traq_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table traq_wiki
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_wiki`;

CREATE TABLE `traq_wiki` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `main` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_wiki` WRITE;
/*!40000 ALTER TABLE `traq_wiki` DISABLE KEYS */;

INSERT INTO `traq_wiki` (`id`, `project_id`, `title`, `slug`, `body`, `main`)
VALUES
	(1,1,'Main','main','Main page!',1),
	(2,1,'Todo','todo','- Something\n- Something else\n   - Another thing',0);

/*!40000 ALTER TABLE `traq_wiki` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
