CREATE TABLE `traq_components` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `default` smallint(6) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_milestones` (
  `id` bigint(20) NOT NULL auto_increment,
  `milestone` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `codename` varchar(255) NOT NULL,
  `info` longtext NOT NULL,
  `due` bigint(20) NOT NULL,
  `completed` bigint(20) NOT NULL,
  `cancelled` bigint(20) NOT NULL,
  `locked` smallint(6) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `displayorder` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_plugins` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `version` varchar(20) NOT NULL,
  `enabled` bigint(20) NOT NULL,
  `install_sql` longtext NOT NULL,
  `uninstall_sql` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_plugin_code` (
  `id` bigint(20) NOT NULL auto_increment,
  `plugin_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `hook` mediumtext NOT NULL,
  `code` longtext NOT NULL,
  `execorder` bigint(20) NOT NULL,
  `enabled` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_priorities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `traq_projects` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `codename` varchar(255) NOT NULL,
  `info` longtext NOT NULL,
  `managers` mediumtext NOT NULL,
  `private` smallint(6) NOT NULL,
  `next_tid` bigint(20) NOT NULL,
  `displayorder` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_settings` (
  `setting` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY  (`setting`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `traq_severities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `traq_tickets` (
  `id` bigint(20) NOT NULL auto_increment,
  `ticket_id` bigint(20) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `milestone_id` bigint(20) NOT NULL,
  `version_id` bigint(20) NOT NULL,
  `component_id` bigint(20) NOT NULL,
  `type` bigint(20) NOT NULL,
  `status` bigint(20) NOT NULL,
  `priority` bigint(20) NOT NULL,
  `severity` bigint(20) NOT NULL,
  `assigned_to` bigint(20) NOT NULL,
  `closed` bigint(20) NOT NULL,
  `created` bigint(20) NOT NULL,
  `updated` bigint(20) NOT NULL,
  `private` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_ticket_history` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `changes` longtext NOT NULL,
  `comment` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `traq_ticket_status` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_ticket_types` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_usergroups` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `is_admin` smallint(6) NOT NULL,
  `create_tickets` smallint(6) NOT NULL,
  `update_tickets` smallint(6) NOT NULL,
  `delete_tickets` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_users` (
  `id` bigint(20) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `group_id` varchar(255) NOT NULL default '2',
  `sesshash` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_versions` (
  `id` bigint(20) NOT NULL auto_increment,
  `version` varchar(255) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_timeline` (
  `id` bigint(20) NOT NULL auto_increment,
  `project_id` bigint(20) NOT NULL,
  `owner_id` bigint(20) NOT NULL,
  `action` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `traq_settings` VALUES ('title', 'Traq'),
('theme', 'Traq2'),
('locale', 'enus'),
('single_project', '0'),
('recaptcha_pubkey', ''),
('recaptcha_privkey', ''),
('allow_registration', '1');

INSERT INTO `traq_priorities` (`id`, `name`) VALUES 
(1, 'Lowest'),
(2, 'Low'),
(3, 'Normal'),
(4, 'High'),
(5, 'Highest');

INSERT INTO `traq_severities` (`id`, `name`) VALUES 
(1, 'Blocker'),
(2, 'Critical'),
(3, 'Major'),
(4, 'Normal'),
(5, 'Minor'),
(6, 'Trivial');

INSERT INTO `traq_ticket_status` (`id`, `name`, `status`) VALUES 
(1, 'New', 1),
(2, 'Accepted', 1),
(3, 'Closed', 0);

INSERT INTO `traq_ticket_types` VALUES (1, 'Defect'),
(2, 'Feature Request'),
(3, 'Task');

INSERT INTO  `traq_usergroups` (`id`,`name`,`is_admin`,`create_tickets`,`update_tickets`,`delete_tickets`)
VALUES (NULL,'Administrators','1','1','1','1'),
(NULL,'Members','0','1','1','0');