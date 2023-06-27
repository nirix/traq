#
# Traq
# Copyright (C) 2009-2023 Traq.io
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

# Dump of table traq_custom_field_values
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_custom_field_values`;

CREATE TABLE `traq_custom_field_values` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `custom_field_id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dump of table traq_custom_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_custom_fields`;

CREATE TABLE `traq_custom_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `values` longtext,
  `multiple` tinyint(1) NOT NULL DEFAULT '0',
  `default_value` varchar(255) DEFAULT NULL,
  `regex` varchar(255) DEFAULT NULL,
  `min_length` int(11) DEFAULT NULL,
  `max_length` int(11) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL,
  `ticket_type_ids` varchar(255) NOT NULL,
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
  `changelog` longtext COLLATE utf8_unicode_ci NULL,
  `due` datetime DEFAULT NULL,
  `completed_on` datetime DEFAULT NULL,
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

INSERT INTO `traq_permissions` (`project_id`, `type`, `type_id`, `action`, `value`)
VALUES
  (0,'usergroup',0,'view',1),
  (0,'usergroup',0,'project_settings',0),
  (0,'usergroup',0,'delete_timeline_events',0),
  (0,'usergroup',0,'view_tickets',1),
  (0,'usergroup',0,'create_tickets',1),
  (0,'usergroup',0,'update_tickets',1),
  (0,'usergroup',0,'delete_tickets',0),
  (0,'usergroup',0,'move_tickets',0),
  (0,'usergroup',0,'comment_on_tickets',1),
  (0,'usergroup',0,'edit_ticket_description',0),
  (0,'usergroup',0,'vote_on_tickets',1),
  (0,'usergroup',0,'add_attachments',1),
  (0,'usergroup',0,'view_attachments',1),
  (0,'usergroup',0,'delete_attachments',0),
  (0,'usergroup',0,'perform_mass_actions',0),
  (0,'usergroup',0,'ticket_properties_set_assigned_to',0),
  (0,'usergroup',0,'ticket_properties_set_milestone',0),
  (0,'usergroup',0,'ticket_properties_set_version',0),
  (0,'usergroup',0,'ticket_properties_set_component',0),
  (0,'usergroup',0,'ticket_properties_set_severity',0),
  (0,'usergroup',0,'ticket_properties_set_priority',0),
  (0,'usergroup',0,'ticket_properties_set_status',0),
  (0,'usergroup',0,'ticket_properties_set_tasks',0),
  (0,'usergroup',0,'ticket_properties_set_related_tickets',0),
  (0,'usergroup',0,'ticket_properties_set_time_proposed',0),
  (0,'usergroup',0,'ticket_properties_set_time_worked',0),
  (0,'usergroup',0,'ticket_properties_change_type',0),
  (0,'usergroup',0,'ticket_properties_change_assigned_to',0),
  (0,'usergroup',0,'ticket_properties_change_milestone',0),
  (0,'usergroup',0,'ticket_properties_change_version',0),
  (0,'usergroup',0,'ticket_properties_change_component',1),
  (0,'usergroup',0,'ticket_properties_change_severity',0),
  (0,'usergroup',0,'ticket_properties_change_priority',0),
  (0,'usergroup',0,'ticket_properties_change_status',0),
  (0,'usergroup',0,'ticket_properties_change_summary',0),
  (0,'usergroup',0,'ticket_properties_change_tasks',0),
  (0,'usergroup',0,'ticket_properties_change_related_tickets',0),
  (0,'usergroup',0,'ticket_properties_change_time_proposed',0),
  (0,'usergroup',0,'ticket_properties_change_time_worked',0),
  (0,'usergroup',0,'ticket_properties_complete_tasks',0),
  (0,'usergroup',0,'edit_ticket_history',0),
  (0,'usergroup',0,'delete_ticket_history',0),
  (0,'usergroup',0,'create_wiki_page',0),
  (0,'usergroup',0,'edit_wiki_page',0),
  (0,'usergroup',0,'delete_wiki_page',0),
  (0,'usergroup',3,'create_tickets',0),
  (0,'usergroup',3,'comment_on_tickets',0),
  (0,'usergroup',3,'update_tickets',0),
  (0,'usergroup',3,'vote_on_tickets',0),
  (0,'usergroup',3,'add_attachments',0),
  (0,'role',0,'view',1),
  (0,'role',0,'project_settings',0),
  (0,'role',0,'delete_timeline_events',0),
  (0,'role',0,'view_tickets',1),
  (0,'role',0,'create_tickets',1),
  (0,'role',0,'update_tickets',1),
  (0,'role',0,'delete_tickets',0),
  (0,'role',0,'move_tickets',0),
  (0,'role',0,'comment_on_tickets',1),
  (0,'role',0,'edit_ticket_description',0),
  (0,'role',0,'vote_on_tickets',1),
  (0,'role',0,'add_attachments',1),
  (0,'role',0,'view_attachments',1),
  (0,'role',0,'delete_attachments',0),
  (0,'role',0,'perform_mass_actions',0),
  (0,'role',0,'ticket_properties_set_assigned_to',1),
  (0,'role',0,'ticket_properties_set_milestone',1),
  (0,'role',0,'ticket_properties_set_version',1),
  (0,'role',0,'ticket_properties_set_component',1),
  (0,'role',0,'ticket_properties_set_severity',1),
  (0,'role',0,'ticket_properties_set_priority',1),
  (0,'role',0,'ticket_properties_set_status',1),
  (0,'role',0,'ticket_properties_set_tasks',1),
  (0,'role',0,'ticket_properties_set_related_tickets',1),
  (0,'role',0,'ticket_properties_set_time_proposed',1),
  (0,'role',0,'ticket_properties_set_time_worked',1),
  (0,'role',0,'ticket_properties_change_type',1),
  (0,'role',0,'ticket_properties_change_assigned_to',1),
  (0,'role',0,'ticket_properties_change_milestone',1),
  (0,'role',0,'ticket_properties_change_version',1),
  (0,'role',0,'ticket_properties_change_component',1),
  (0,'role',0,'ticket_properties_change_severity',1),
  (0,'role',0,'ticket_properties_change_priority',1),
  (0,'role',0,'ticket_properties_change_status',1),
  (0,'role',0,'ticket_properties_change_summary',1),
  (0,'role',0,'ticket_properties_change_tasks',1),
  (0,'role',0,'ticket_properties_change_related_tickets',1),
  (0,'role',0,'ticket_properties_change_time_proposed',1),
  (0,'role',0,'ticket_properties_change_time_worked',1),
  (0,'role',0,'ticket_properties_complete_tasks',1),
  (0,'role',0,'edit_ticket_history',0),
  (0,'role',0,'delete_ticket_history',0),
  (0,'role',0,'create_wiki_page',0),
  (0,'role',0,'edit_wiki_page',0),
  (0,'role',0,'delete_wiki_page',0),
  (0,'role',1,'project_settings',1),
  (0,'role',1,'delete_timeline_events',1),
  (0,'role',1,'delete_tickets',1),
  (0,'role',1,'move_tickets',1),
  (0,'role',1,'edit_ticket_description',1),
  (0,'role',1,'delete_attachments',1),
  (0,'role',1,'edit_ticket_history',1),
  (0,'role',1,'delete_ticket_history',1),
  (0,'role',1,'perform_mass_actions',1),
  (0,'role',1,'create_wiki_page',1),
  (0,'role',1,'edit_wiki_page',1),
  (0,'role',1,'delete_wiki_page',1);

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
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_priorities` WRITE;

INSERT INTO `traq_priorities` (`id`, `name`)
VALUES
	(1,'Highest'),
	(2,'High'),
	(3,'Normal'),
	(4,'Low'),
	(5,'Lowest');

UNLOCK TABLES;

# Dump of table traq_project_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_project_roles`;

CREATE TABLE `traq_project_roles` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `assignable` varchar(255) NOT NULL DEFAULT '1',
  `project_id` bigint(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `traq_project_roles` WRITE;

INSERT INTO `traq_project_roles` (`id`, `name`, `assignable`, `project_id`)
VALUES
	(1,'Manager',1,0),
	(2,'Developer',1,0),
	(3,'Tester',0,0);

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
  `default_ticket_type_id` int(11) DEFAULT NULL,
  `default_ticket_sorting` varchar(255) NOT NULL DEFAULT 'priority.asc',
  `displayorder` bigint(20) NOT NULL DEFAULT '0',
  `private_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
  ('allow_registration', '1'),
  ('email_validation', '0'),
  ('check_for_update', '1'),
  ('date_time_format', 'g:iA d/m/Y'),
  ('date_format', 'd/m/Y'),
  ('locale', 'enus'),
  ('theme', 'default'),
  ('ticket_creation_delay', '30'),
  ('ticket_history_sorting', 'oldest_first'),
  ('tickets_per_page', '25'),
  ('timeline_day_format', 'l, jS F Y'),
  ('timeline_days_per_page', '7'),
  ('timeline_time_format', 'h:iA'),
  ('title', 'Traq'),
  ('site_name', ''),
  ('site_url', ''),
  ('mailer_config', 'config'),
  ('mailer_dsn', 'sendmail://default');

UNLOCK TABLES;

# Dump of table traq_ticket_relationships
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `traq_ticket_relationships` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) NOT NULL,
  `related_ticket_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

# Dump of table traq_severities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_severities`;

CREATE TABLE `traq_severities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `object_id` bigint(20) NOT NULL,
  `uuid` varchar(36) NOT NULL,
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

# Dump of table traq_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_statuses`;

CREATE TABLE `traq_statuses` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL,
  `changelog` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_statuses` WRITE;

INSERT INTO `traq_statuses` (`id`, `name`, `status`, `changelog`)
VALUES
  (1, 'New', 1, 0),
  (2, 'Accepted', 1, 0),
  (3, 'Started', 2, 0),
  (4, 'Closed', 0, 1),
  (5, 'Completed', 0, 1);

UNLOCK TABLES;

# Dump of table traq_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_types`;

CREATE TABLE `traq_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bullet` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `changelog` smallint(6) NOT NULL DEFAULT '1',
  `template` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `traq_types` WRITE;

INSERT INTO `traq_types` (`id`, `name`, `bullet`, `changelog`, `template`)
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
  `milestone_id` bigint(20) NOT NULL default '0',
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
  `tasks` longtext COLLATE utf8_unicode_ci NULL,
  `extra` longtext COLLATE utf8_unicode_ci NOT NULL,
  `time_proposed` varchar(255),
  `time_worked` varchar(255),
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
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
  `data` longtext COLLATE utf8_unicode_ci NULL,
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
  `locale` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `options` text COLLATE utf8_unicode_ci,
  `login_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `api_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
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
  `main` smallint(6) NOT NULL DEFAULT '0',
  `revision_id` bigint(20) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Dump of table traq_wiki_revisions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `traq_wiki_revisions`;

CREATE TABLE `traq_wiki_revisions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `wiki_page_id` bigint(20) NOT NULL,
  `revision` bigint(20) NOT NULL DEFAULT '1',
  `content` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
