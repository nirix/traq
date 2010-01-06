CREATE TABLE `traq_attachments` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `contents` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `ownerid` bigint(20) NOT NULL,
  `ownername` varchar(255) NOT NULL,
  `ticketid` bigint(20) NOT NULL,
  `projectid` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_components` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `desc` longtext NOT NULL,
  `project` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_milestones` (
  `id` bigint(20) NOT NULL auto_increment,
  `milestone` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `codename` varchar(255) NOT NULL,
  `desc` longtext NOT NULL,
  `project` bigint(20) NOT NULL,
  `due` bigint(20) NOT NULL,
  `completed` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_plugins` (
  `file` varchar(255) NOT NULL,
  PRIMARY KEY  (`file`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `traq_priorities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `traq_projects` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `desc` longtext NOT NULL,
  `managers` longtext NOT NULL,
  `currenttid` bigint(20) NOT NULL,
  `sourcelocation` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_settings` (
  `setting` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY  (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `traq_severities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `traq_statustypes` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `traq_tickethistory` (
  `id` bigint(20) NOT NULL auto_increment,
  `timestamp` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `ticketid` bigint(20) NOT NULL,
  `changes` longtext NOT NULL,
  `comment` longtext NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_tickets` (
  `id` bigint(20) NOT NULL auto_increment,
  `tid` bigint(20) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `projectid` bigint(20) NOT NULL,
  `milestoneid` bigint(20) NOT NULL,
  `versionid` bigint(20) NOT NULL,
  `componentid` bigint(20) NOT NULL,
  `type` bigint(1) NOT NULL,
  `status` bigint(20) NOT NULL,
  `priority` bigint(20) NOT NULL,
  `severity` bigint(20) NOT NULL,
  `ownerid` bigint(20) NOT NULL,
  `ownername` varchar(255) NOT NULL,
  `assigneeid` bigint(20) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `updated` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_timeline` (
  `id` bigint(20) NOT NULL auto_increment,
  `type` bigint(20) NOT NULL,
  `data` longtext NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `userid` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `projectid` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_types` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_usergroups` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `isadmin` smallint(6) NOT NULL default '0',
  `createtickets` smallint(1) NOT NULL,
  `updatetickets` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_users` (
  `id` bigint(255) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL default 'Guest',
  `password` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `groupid` bigint(20) NOT NULL default '3',
  `hash` varchar(255) NOT NULL default '',
  UNIQUE KEY `uid` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_versions` (
  `id` bigint(20) NOT NULL auto_increment,
  `version` varchar(255) NOT NULL,
  `projectid` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `traq_priorities` (`id`, `name`) VALUES 
(1, 'Lowest'),
(2, 'Low'),
(3, 'Normal'),
(4, 'High'),
(5, 'Highest');

INSERT INTO `traq_settings` (`setting`, `value`) VALUES 
('theme', 'default'),
('uritype','2'),
('akismetkey',''),
('langfile','enus');

INSERT INTO `traq_severities` (`id`, `name`) VALUES 
(1, 'Blocker'),
(2, 'Critical'),
(3, 'Major'),
(4, 'Normal'),
(5, 'Minor'),
(6, 'Trivial');

INSERT INTO `traq_statustypes` (`id`, `name`) VALUES 
(-3, 'Fixed'),
(-2, 'Invalid'),
(-1, 'Completed'),
(0, 'Closed'),
(1, 'New'),
(2, 'Accepted'),
(3, 'Reopened'),
(4, 'Started');

INSERT INTO `traq_types` (`id`, `name`) VALUES 
(1, 'Defect'),
(2, 'Enhancement'),
(3, 'Feature Request'),
(4, 'Task');

INSERT INTO `traq_usergroups` (`id`, `name`, `isadmin`, `createtickets`, `updatetickets`) VALUES 
(1, 'Admins', 1, 1, 1),
(2, 'Members', 0, 1, 1),
(3, 'Guests', 0, 0, 0);