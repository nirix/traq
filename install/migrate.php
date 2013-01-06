<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

require './bootstrap.php';

use avalon\Database;
use avalon\output\View;
use avalon\helpers\Time;

// Models baby, models.
use traq\models\Attachment;
use traq\models\Component;
use traq\models\Group;
use traq\models\Milestone;
use traq\models\Priority;
use traq\models\Project;
use traq\models\Severity;
use traq\models\Status;
use traq\models\Ticket;
use traq\models\TicketHistory;
use traq\models\Timeline;
use traq\models\Type;
use traq\models\User;
use traq\models\UserRole;
use traq\models\WikiPage;

// URI helper
require '../vendor/traq/helpers/uri.php';

// Set page and title
View::set('page', 'migrate');
View::set('page_title', 'Migration');

// Make sure the config file doesn't exist...
if (!file_exists('../vendor/traq/config/database.php')) {
    Error::halt('Error', 'Config file not found.');
}

// Index
get('/', function(){
    View::set('title', 'License Agreement');
    render('index');
});

// Step 1
post('/step/1', function(){
    View::set('title', 'Authentication');
    render('migrate/auth');
});

// Step 2
post('/step/2', function(){
    $db = get_connection();

    // Verify user
    $user = $db->select()->from('users')->custom_sql("WHERE `username`='{$_POST['username']}' AND `password`='" . sha1($_POST['password']) . "' AND `group_id`=1 LIMIT 1");
    if (!$user->exec()->row_count()) {
        View::set('title', 'Authentication');
        View::set('error', true);
        render('migrate/auth');
    }
    // Confirm
    else {
        View::set('title', 'Confirmation');
        render('migrate/confirm');
    }
});

// Step 3
post('/step/3', function(){
    $db = get_connection();
    $_SESSION['migrating'] = true;

    // Get attachments
    $attachments = $db->select()->from('attachments')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
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
    ");

    // Add attachments
    foreach ($attachments as $attachment) {
        $attach = new Attachment(array(
            'id'         => $attachment['id'],
            'name'       => $attachment['name'],
            'contents'   => $attachment['contents'],
            'type'       => $attachment['type'],
            'size'       => $attachment['size'],
            'user_id'    => $attachment['owner_id'],
            'ticket_id'  => $attachment['ticket_id'],
            'created_at' => Time::date("Y-m-d H:i:s", $attachment['uploaded'])
        ));
        $attach->save();
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/4');
});

// Step 4
get('/step/4', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get components
    $components = $db->select()->from('components')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
        DROP TABLE IF EXISTS `traq_components`;
        CREATE TABLE `traq_components` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `project_id` bigint(20) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");

    // Add components
    foreach ($components as $component) {
        $comp = new Component(array(
            'id'         => $component['id'],
            'name'       => $component['name'],
            'project_id' => $component['project_id']
        ));
        $comp->save();
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/5');
});

// Step 5
get('/step/5', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get milestones
    $milestones = $db->select()->from('milestones')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
        DROP TABLE IF EXISTS `traq_milestones`;
        CREATE TABLE `traq_milestones` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `codename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `info` longtext COLLATE utf8_unicode_ci NOT NULL,
          `changelog` longtext COLLATE utf8_unicode_ci NOT NULL,
          `due` datetime DEFAULT NULL,
          `status` int(1) NOT NULL DEFAULT '1',
          `is_locked` smallint(6) NOT NULL DEFAULT '0',
          `project_id` bigint(20) NOT NULL,
          `displayorder` bigint(20) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");

    // Add milestones
    foreach ($milestones as $milestone) {
        $ms = new Milestone(array(
            'id'           => $milestone['id'],
            'name'         => $milestone['milestone'],
            'slug'         => $milestone['slug'],
            'codename'     => $milestone['codename'],
            'info'         => $milestone['info'],
            'changelog'    => $milestone['changelog'],
            'due'          => $milestone['due'] == 0 ? null : Time::date("Y-m-d H:i:s", $milestone['due']),
            'status'       => $milestone['locked'] ? 0 : 1,
            'is_locked'    => $milestone['locked'],
            'project_id'   => $milestone['project_id'],
            'displayorder' => $milestone['displayorder']
        ));
        $ms->save();
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/6');
});

// Step 6
get('/step/6', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get projects
    $projects = $db->select()->from('projects')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
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
    ");

    // Add projects
    foreach ($projects as $project) {
        $proj = new Project(array(
            'id'           => $project['id'],
            'name'         => $project['name'],
            'slug'         => $project['slug'],
            'codename'     => $project['codename'],
            'info'         => $project['info'],
            'next_tid'     => $project['next_tid'],
            'enable_wiki'  => 1,
            'displayorder' => $project['displayorder'],
            'private_key'  => ''
        ));
        $proj->save();
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/7');
});
