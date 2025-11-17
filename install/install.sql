--
-- Traq
-- Copyright (C) 2009-2025 Traq.io
--
-- This file is part of Traq.
--
-- Traq is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; version 3 only.
--
-- Traq is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with Traq. If not, see <http://www.gnu.org/licenses/>.
--

CREATE TABLE `attachments` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contents` longtext NOT NULL,
  `type` varchar(255) NOT NULL,
  `size` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `components` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `project_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `custom_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `values` longtext DEFAULT NULL,
  `multiple` tinyint(1) NOT NULL DEFAULT 0,
  `default_value` varchar(255) DEFAULT NULL,
  `regex` varchar(255) DEFAULT NULL,
  `min_length` int(11) DEFAULT NULL,
  `max_length` int(11) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `project_id` int(11) NOT NULL,
  `ticket_type_ids` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `custom_field_values` (
  `id` int(11) UNSIGNED NOT NULL,
  `custom_field_id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `milestones` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL,
  `codename` varchar(255) NOT NULL,
  `info` longtext NOT NULL,
  `changelog` longtext DEFAULT NULL,
  `due` datetime DEFAULT NULL,
  `completed_on` datetime DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `is_locked` smallint(6) NOT NULL DEFAULT 0,
  `project_id` bigint(20) NOT NULL,
  `displayorder` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `permissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `project_id` bigint(20) NOT NULL DEFAULT 0,
  `type` varchar(255) DEFAULT NULL,
  `type_id` bigint(20) NOT NULL DEFAULT 0,
  `action` varchar(255) NOT NULL DEFAULT '',
  `value` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

INSERT INTO `permissions` (`id`, `project_id`, `type`, `type_id`, `action`, `value`) VALUES
(1, 0, 'usergroup', 0, 'view', 1),
(2, 0, 'usergroup', 0, 'project_settings', 0),
(3, 0, 'usergroup', 0, 'delete_timeline_events', 0),
(4, 0, 'usergroup', 0, 'view_tickets', 1),
(5, 0, 'usergroup', 0, 'create_tickets', 1),
(6, 0, 'usergroup', 0, 'update_tickets', 1),
(7, 0, 'usergroup', 0, 'delete_tickets', 0),
(8, 0, 'usergroup', 0, 'move_tickets', 0),
(9, 0, 'usergroup', 0, 'comment_on_tickets', 1),
(10, 0, 'usergroup', 0, 'edit_ticket_description', 0),
(11, 0, 'usergroup', 0, 'vote_on_tickets', 1),
(12, 0, 'usergroup', 0, 'add_attachments', 1),
(13, 0, 'usergroup', 0, 'view_attachments', 1),
(14, 0, 'usergroup', 0, 'delete_attachments', 0),
(15, 0, 'usergroup', 0, 'perform_mass_actions', 0),
(16, 0, 'usergroup', 0, 'ticket_properties_set_assigned_to', 0),
(17, 0, 'usergroup', 0, 'ticket_properties_set_milestone', 0),
(18, 0, 'usergroup', 0, 'ticket_properties_set_version', 0),
(19, 0, 'usergroup', 0, 'ticket_properties_set_component', 0),
(20, 0, 'usergroup', 0, 'ticket_properties_set_severity', 0),
(21, 0, 'usergroup', 0, 'ticket_properties_set_priority', 0),
(22, 0, 'usergroup', 0, 'ticket_properties_set_status', 0),
(23, 0, 'usergroup', 0, 'ticket_properties_set_tasks', 0),
(24, 0, 'usergroup', 0, 'ticket_properties_set_related_tickets', 0),
(25, 0, 'usergroup', 0, 'ticket_properties_set_time_proposed', 0),
(26, 0, 'usergroup', 0, 'ticket_properties_set_time_worked', 0),
(27, 0, 'usergroup', 0, 'ticket_properties_change_type', 0),
(28, 0, 'usergroup', 0, 'ticket_properties_change_assigned_to', 0),
(29, 0, 'usergroup', 0, 'ticket_properties_change_milestone', 0),
(30, 0, 'usergroup', 0, 'ticket_properties_change_version', 0),
(31, 0, 'usergroup', 0, 'ticket_properties_change_component', 1),
(32, 0, 'usergroup', 0, 'ticket_properties_change_severity', 0),
(33, 0, 'usergroup', 0, 'ticket_properties_change_priority', 0),
(34, 0, 'usergroup', 0, 'ticket_properties_change_status', 0),
(35, 0, 'usergroup', 0, 'ticket_properties_change_summary', 0),
(36, 0, 'usergroup', 0, 'ticket_properties_change_tasks', 0),
(37, 0, 'usergroup', 0, 'ticket_properties_change_related_tickets', 0),
(38, 0, 'usergroup', 0, 'ticket_properties_change_time_proposed', 0),
(39, 0, 'usergroup', 0, 'ticket_properties_change_time_worked', 0),
(40, 0, 'usergroup', 0, 'ticket_properties_complete_tasks', 0),
(41, 0, 'usergroup', 0, 'edit_ticket_history', 0),
(42, 0, 'usergroup', 0, 'delete_ticket_history', 0),
(43, 0, 'usergroup', 0, 'create_wiki_page', 0),
(44, 0, 'usergroup', 0, 'edit_wiki_page', 0),
(45, 0, 'usergroup', 0, 'delete_wiki_page', 0),
(46, 0, 'usergroup', 3, 'create_tickets', 0),
(47, 0, 'usergroup', 3, 'comment_on_tickets', 0),
(48, 0, 'usergroup', 3, 'update_tickets', 0),
(49, 0, 'usergroup', 3, 'vote_on_tickets', 0),
(50, 0, 'usergroup', 3, 'add_attachments', 0),
(51, 0, 'role', 0, 'view', 1),
(52, 0, 'role', 0, 'project_settings', 0),
(53, 0, 'role', 0, 'delete_timeline_events', 0),
(54, 0, 'role', 0, 'view_tickets', 1),
(55, 0, 'role', 0, 'create_tickets', 1),
(56, 0, 'role', 0, 'update_tickets', 1),
(57, 0, 'role', 0, 'delete_tickets', 0),
(58, 0, 'role', 0, 'move_tickets', 0),
(59, 0, 'role', 0, 'comment_on_tickets', 1),
(60, 0, 'role', 0, 'edit_ticket_description', 0),
(61, 0, 'role', 0, 'vote_on_tickets', 1),
(62, 0, 'role', 0, 'add_attachments', 1),
(63, 0, 'role', 0, 'view_attachments', 1),
(64, 0, 'role', 0, 'delete_attachments', 0),
(65, 0, 'role', 0, 'perform_mass_actions', 0),
(66, 0, 'role', 0, 'ticket_properties_set_assigned_to', 1),
(67, 0, 'role', 0, 'ticket_properties_set_milestone', 1),
(68, 0, 'role', 0, 'ticket_properties_set_version', 1),
(69, 0, 'role', 0, 'ticket_properties_set_component', 1),
(70, 0, 'role', 0, 'ticket_properties_set_severity', 1),
(71, 0, 'role', 0, 'ticket_properties_set_priority', 1),
(72, 0, 'role', 0, 'ticket_properties_set_status', 1),
(73, 0, 'role', 0, 'ticket_properties_set_tasks', 1),
(74, 0, 'role', 0, 'ticket_properties_set_related_tickets', 1),
(75, 0, 'role', 0, 'ticket_properties_set_time_proposed', 1),
(76, 0, 'role', 0, 'ticket_properties_set_time_worked', 1),
(77, 0, 'role', 0, 'ticket_properties_change_type', 1),
(78, 0, 'role', 0, 'ticket_properties_change_assigned_to', 1),
(79, 0, 'role', 0, 'ticket_properties_change_milestone', 1),
(80, 0, 'role', 0, 'ticket_properties_change_version', 1),
(81, 0, 'role', 0, 'ticket_properties_change_component', 1),
(82, 0, 'role', 0, 'ticket_properties_change_severity', 1),
(83, 0, 'role', 0, 'ticket_properties_change_priority', 1),
(84, 0, 'role', 0, 'ticket_properties_change_status', 1),
(85, 0, 'role', 0, 'ticket_properties_change_summary', 1),
(86, 0, 'role', 0, 'ticket_properties_change_tasks', 1),
(87, 0, 'role', 0, 'ticket_properties_change_related_tickets', 1),
(88, 0, 'role', 0, 'ticket_properties_change_time_proposed', 1),
(89, 0, 'role', 0, 'ticket_properties_change_time_worked', 1),
(90, 0, 'role', 0, 'ticket_properties_complete_tasks', 1),
(91, 0, 'role', 0, 'edit_ticket_history', 0),
(92, 0, 'role', 0, 'delete_ticket_history', 0),
(93, 0, 'role', 0, 'create_wiki_page', 0),
(94, 0, 'role', 0, 'edit_wiki_page', 0),
(95, 0, 'role', 0, 'delete_wiki_page', 0),
(96, 0, 'role', 1, 'project_settings', 1),
(97, 0, 'role', 1, 'delete_timeline_events', 1),
(98, 0, 'role', 1, 'delete_tickets', 1),
(99, 0, 'role', 1, 'move_tickets', 1),
(100, 0, 'role', 1, 'edit_ticket_description', 1),
(101, 0, 'role', 1, 'delete_attachments', 1),
(102, 0, 'role', 1, 'edit_ticket_history', 1),
(103, 0, 'role', 1, 'delete_ticket_history', 1),
(104, 0, 'role', 1, 'perform_mass_actions', 1),
(105, 0, 'role', 1, 'create_wiki_page', 1),
(106, 0, 'role', 1, 'edit_wiki_page', 1),
(107, 0, 'role', 1, 'delete_wiki_page', 1);

CREATE TABLE `plugins` (
  `id` bigint(20) NOT NULL,
  `file` varchar(255) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `plugins` (`id`, `file`, `enabled`) VALUES
(1, 'markdown', 1);

CREATE TABLE `priorities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `priorities` (`id`, `name`) VALUES
(1, 'Highest'),
(2, 'High'),
(3, 'Normal'),
(4, 'Low'),
(5, 'Lowest');

CREATE TABLE `projects` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `codename` varchar(255) NOT NULL,
  `info` longtext NOT NULL,
  `next_tid` bigint(20) NOT NULL DEFAULT 1,
  `enable_wiki` tinyint(1) NOT NULL DEFAULT 0,
  `default_ticket_type_id` int(11) DEFAULT NULL,
  `default_ticket_sorting` varchar(255) NOT NULL DEFAULT 'priority.asc',
  `displayorder` bigint(20) NOT NULL DEFAULT 0,
  `private_key` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `project_roles` (
  `id` int(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `assignable` varchar(255) NOT NULL DEFAULT '1',
  `project_id` bigint(20) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

INSERT INTO `project_roles` (`id`, `name`, `assignable`, `project_id`) VALUES
(1, 'Manager', '1', 0),
(2, 'Developer', '1', 0),
(3, 'Tester', '0', 0);

CREATE TABLE `settings` (
  `setting` varchar(255) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `settings` (`setting`, `value`) VALUES
('allow_registration', '1'),
('check_for_update', '1'),
('date_format', 'd/m/Y'),
('date_time_format', 'g:iA d/m/Y'),
('email_validation', '0'),
('locale', 'enus'),
('mailer_config', 'config'),
('mailer_dsn', 'sendmail://default'),
('site_name', ''),
('site_url', ''),
('theme', 'default'),
('ticket_creation_delay', '30'),
('ticket_history_sorting', 'oldest_first'),
('tickets_per_page', '25'),
('timeline_day_format', 'l, jS F Y'),
('timeline_days_per_page', '7'),
('timeline_time_format', 'h:iA'),
('title', 'Traq');

CREATE TABLE `severities` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `severities` (`id`, `name`) VALUES
(1, 'Blocker'),
(2, 'Critical'),
(3, 'Major'),
(4, 'Normal'),
(5, 'Minor'),
(6, 'Trivial');

CREATE TABLE `statuses` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL,
  `changelog` smallint(6) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `statuses` (`id`, `name`, `status`, `changelog`) VALUES
(1, 'New', 1, 0),
(2, 'Accepted', 1, 0),
(3, 'Started', 2, 0),
(4, 'Closed', 0, 1),
(5, 'Completed', 0, 1);

CREATE TABLE `subscriptions` (
  `id` bigint(20) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `object_id` bigint(20) NOT NULL,
  `uuid` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `tickets` (
  `id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `milestone_id` bigint(20) DEFAULT NULL,
  `version_id` bigint(20) DEFAULT NULL,
  `component_id` bigint(20) DEFAULT NULL,
  `type_id` bigint(20) NOT NULL,
  `status_id` bigint(20) NOT NULL DEFAULT 1,
  `priority_id` bigint(20) NOT NULL DEFAULT 3,
  `severity_id` bigint(20) NOT NULL,
  `assigned_to_id` bigint(20) DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `votes` bigint(20) DEFAULT 0,
  `tasks` longtext DEFAULT NULL,
  `extra` longtext NOT NULL,
  `time_proposed` varchar(255) DEFAULT NULL,
  `time_worked` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `ticket_history` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `changes` longtext NOT NULL,
  `comment` longtext NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `ticket_relationships` (
  `id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `related_ticket_id` bigint(20) NOT NULL,
  `relation_type_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `ticket_relation_types` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(100) NOT NULL,
  `inverse_type_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

INSERT INTO `ticket_relation_types` (`id`, `name`, `inverse_type_id`) VALUES
(1, 'Relates to', 1),
(2, 'Blocks', 3),
(3, 'Is Blocked By', 2),
(4, 'Duplicates', 5),
(5, 'Is Duplicated By', 4),
(6, 'Contains', 7),
(7, 'Is Contained By', 6);

CREATE TABLE `timeline` (
  `id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `owner_id` bigint(20) NOT NULL,
  `action` varchar(255) NOT NULL,
  `data` longtext DEFAULT NULL,
  `user_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `types` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `bullet` varchar(10) NOT NULL,
  `changelog` smallint(6) NOT NULL DEFAULT 1,
  `template` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `types` (`id`, `name`, `bullet`, `changelog`, `template`) VALUES
(1, 'Defect', '-', 1, ''),
(2, 'Feature Request', '+', 1, ''),
(3, 'Enhancement', '*', 1, ''),
(4, 'Task', '*', 1, '');

CREATE TABLE `usergroups` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_admin` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `usergroups` (`id`, `name`, `is_admin`) VALUES
(1, 'Administrators', 1),
(2, 'Members', 0),
(3, 'Guests', 0);

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_ver` varchar(25) DEFAULT 'crypt',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `group_id` bigint(20) NOT NULL DEFAULT 2,
  `locale` varchar(255) DEFAULT NULL,
  `options` text DEFAULT NULL,
  `login_hash` varchar(255) NOT NULL DEFAULT '0',
  `api_key` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `user_roles` (
  `id` int(20) UNSIGNED NOT NULL,
  `user_id` int(20) DEFAULT NULL,
  `project_id` int(20) DEFAULT NULL,
  `project_role_id` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

CREATE TABLE `wiki` (
  `id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `main` smallint(6) NOT NULL DEFAULT 0,
  `revision_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

CREATE TABLE `wiki_revisions` (
  `id` bigint(20) NOT NULL,
  `wiki_page_id` bigint(20) NOT NULL,
  `revision` bigint(20) NOT NULL DEFAULT 1,
  `content` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `components`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `custom_fields`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `custom_field_values`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `milestones`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `plugins`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `priorities`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `project_roles`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting`);

ALTER TABLE `severities`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rl_component` (`component_id`),
  ADD KEY `rl_milestone` (`milestone_id`),
  ADD KEY `rl_priority` (`priority_id`),
  ADD KEY `rl_project` (`project_id`),
  ADD KEY `rl_user` (`user_id`),
  ADD KEY `rl_version` (`version_id`),
  ADD KEY `rl_type` (`type_id`);

ALTER TABLE `ticket_history`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ticket_relationships`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `timeline`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usergroups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `wiki`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `wiki_revisions`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `attachments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `components`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `custom_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `custom_field_values`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `milestones`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `permissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

ALTER TABLE `plugins`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `priorities`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `projects`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `project_roles`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `severities`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `statuses`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tickets`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ticket_history`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ticket_relationships`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ticket_relation_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_relationship_type` (`inverse_type_id`);

ALTER TABLE `timeline`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `types`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `usergroups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_roles`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `wiki`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `wiki_revisions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;


ALTER TABLE `tickets`
  ADD CONSTRAINT `rl_component` FOREIGN KEY (`component_id`) REFERENCES `components` (`id`),
  ADD CONSTRAINT `rl_milestone` FOREIGN KEY (`milestone_id`) REFERENCES `milestones` (`id`),
  ADD CONSTRAINT `rl_priority` FOREIGN KEY (`priority_id`) REFERENCES `priorities` (`id`),
  ADD CONSTRAINT `rl_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `rl_type` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`),
  ADD CONSTRAINT `rl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rl_version` FOREIGN KEY (`version_id`) REFERENCES `milestones` (`id`);
