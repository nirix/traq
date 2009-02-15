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
  `milestone` varchar(255) NOT NULL,
  `codename` varchar(255) NOT NULL,
  `desc` longtext NOT NULL,
  `project` bigint(20) NOT NULL,
  `due` bigint(20) NOT NULL,
  `completed` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_projects` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `desc` longtext NOT NULL,
  `managers` longtext NOT NULL,
  `currenttid` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_settings` (
  `setting` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY  (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `traq_tickethistory` (
  `id` bigint(20) NOT NULL auto_increment,
  `timestamp` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
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
  `assigneeid` bigint(20) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `updated` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_timeline` (
  `id` bigint(20) NOT NULL,
  `type` bigint(20) NOT NULL,
  `data` longtext NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `userid` bigint(20) NOT NULL,
  `projectid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `traq_usergroups` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `isadmin` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_users` (
  `uid` bigint(255) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL default 'Guest',
  `password` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `groupid` bigint(20) NOT NULL default '3',
  `hash` varchar(255) NOT NULL default '',
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `traq_versions` (
  `id` bigint(20) NOT NULL auto_increment,
  `version` varchar(255) NOT NULL,
  `projectid` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `traq_settings` (`setting`, `value`) VALUES 
('theme', 'default');

INSERT INTO `traq_usergroups` (`id`, `name`, `isadmin`, `updatetickets`) VALUES 
(1, 'Admins', 1, 1),
(2, 'Members', 0, 0),
(3, 'Guests', 0, 0);