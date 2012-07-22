#
# Traq
# Copyright (C) 2009-2012 Traq.io
# 
# This file is part of Traq.
# 
# Traq is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; version 3 only.
# 
# Traq is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Traq. If not, see <http://www.gnu.org/licenses/>.
#

# Dump of table traq_attachments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_attachments`;

CREATE TABLE `traq_attachments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contents` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Dump of table traq_components
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_components`;

CREATE TABLE `traq_components` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `project_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `due` datetime NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `is_locked` smallint(6) NOT NULL DEFAULT '0',
  `project_id` bigint(20) NOT NULL,
  `displayorder` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Dump of table traq_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_permissions`;

CREATE TABLE `traq_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `type_id` bigint(20) NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL DEFAULT '',
  `value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `traq_permissions` WRITE;

INSERT INTO `traq_permissions` (`id`, `project_id`, `type`, `type_id`, `action`, `value`)
VALUES
  (1,0,'usergroup',0,'view',1),
  (2,0,'usergroup',0,'project_settings',0),
  (3,0,'usergroup',0,'comment_on_tickets',1),
  (4,0,'usergroup',0,'update_tickets',1),
  (5,0,'usergroup',0,'edit_ticket_description',0),
  (6,0,'usergroup',0,'vote_on_tickets',1),
  (7,0,'usergroup',0,'add_attachments',1),
  (8,0,'usergroup',0,'view_attachments',1),
  (9,0,'usergroup',0,'delete_attachments',0),
  (10,0,'usergroup',0,'edit_ticket_history',0),
  (11,0,'usergroup',0,'delete_ticket_history',0),
  (12,0,'usergroup',0,'create_wiki_page',0),
  (13,0,'usergroup',0,'edit_wiki_page',0),
  (14,0,'usergroup',0,'delete_wiki_page',0),
  (15,0,'usergroup',3,'comment_on_tickets',0),
  (16,0,'usergroup',3,'update_tickets',0),
  (17,0,'usergroup',3,'vote_on_tickets',0),
  (18,0,'usergroup',3,'add_attachments',0),
  (19,0,'role',0,'view',1),
  (20,0,'role',0,'project_settings',0),
  (21,0,'role',0,'comment_on_tickets',1),
  (22,0,'role',0,'update_tickets',1),
  (23,0,'role',0,'edit_ticket_description',0),
  (24,0,'role',0,'vote_on_tickets',1),
  (25,0,'role',0,'add_attachments',1),
  (26,0,'role',0,'view_attachments',1),
  (27,0,'role',0,'delete_attachments',0),
  (28,0,'role',0,'edit_ticket_history',0),
  (29,0,'role',0,'delete_ticket_history',0),
  (30,0,'role',0,'create_wiki_page',0),
  (31,0,'role',0,'edit_wiki_page',0),
  (32,0,'role',0,'delete_wiki_page',0),
  (33,0,'role',1,'project_settings',1),
  (34,0,'role',1,'edit_ticket_description',1),
  (35,0,'role',1,'delete_attachments',1),
  (36,0,'role',1,'edit_ticket_history',1),
  (37,0,'role',1,'delete_ticket_history',1),
  (38,0,'role',1,'create_wiki_page',1),
  (39,0,'role',1,'edit_wiki_page',1),
  (40,0,'role',1,'delete_wiki_page',1);

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

INSERT INTO `traq_plugins` (`id`, `file`, `enabled`)
VALUES
	(1,'markdown',1);

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

INSERT INTO `traq_priorities` (`id`, `name`)
VALUES
	(1,'Lowest'),
	(2,'Low'),
	(3,'Normal'),
	(4,'High'),
	(5,'Highest');

UNLOCK TABLES;

# Dump of table traq_project_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_project_roles`;

CREATE TABLE `traq_project_roles` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `project_id` bigint(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `traq_project_roles` WRITE;

INSERT INTO `traq_project_roles` (`id`, `name`, `project_id`)
VALUES
	(1,'Manager',0),
	(2,'Developer',0),
	(3,'Tester',0);

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
  `next_tid` bigint(20) NOT NULL DEFAULT '1',
  `enable_wiki` tinyint(1) NOT NULL DEFAULT '0',
  `displayorder` bigint(20) NOT NULL,
  `private_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

INSERT INTO `traq_settings` (`setting`, `value`)
VALUES
	('allow_registration','1'),
	('check_for_update','1'),
	('date_time_format','g:iA d/m/Y'),
	('db_revision','22'),
	('locale','enus'),
	('theme','Default'),
	('timeline_day_format','l, jS F Y'),
	('timeline_time_format','h:iA'),
	('title','Traq');

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

INSERT INTO `traq_severities` (`id`, `name`)
VALUES
	(1,'Blocker'),
	(2,'Critical'),
	(3,'Major'),
	(4,'Normal'),
	(5,'Minor'),
	(6,'Trivial');

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
  `ticket_id` bigint(20) NOT NULL,
  `changes` longtext COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
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

INSERT INTO `traq_ticket_status` (`id`, `name`, `status`, `changelog`)
VALUES
	(1,'New',1,0),
	(2,'Accepted',1,0),
	(3,'Closed',0,1),
	(4,'Completed',0,1);

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

INSERT INTO `traq_ticket_types` (`id`, `name`, `bullet`, `changelog`, `template`)
VALUES
	(1,'Defect','-',1,''),
	(2,'Feature Request','+',1,''),
	(3,'Enhancement','*',1,''),
	(4,'Task','*',1,'');

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
  `priority_id` bigint(20) NOT NULL DEFAULT '3',
  `severity_id` bigint(20) NOT NULL,
  `assigned_to_id` bigint(20) NOT NULL,
  `is_closed` bigint(20) NOT NULL DEFAULT '0',
  `is_private` smallint(6) NOT NULL DEFAULT '0',
  `votes` bigint(20) DEFAULT '0',
  `extra` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Dump of table traq_user_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_user_roles`;

CREATE TABLE `traq_user_roles` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(20) DEFAULT NULL,
  `project_id` int(20) DEFAULT NULL,
  `project_role_id` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

INSERT INTO `traq_usergroups` (`id`, `name`, `is_admin`)
VALUES
	(1,'Administrators',1),
	(2,'Members',0),
	(3,'Guests',0);

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
