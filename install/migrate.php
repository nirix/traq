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
          `completed_on` datetime DEFAULT NULL,
          `status` int(1) NOT NULL DEFAULT '1',
          `is_locked` smallint(6) NOT NULL DEFAULT '0',
          `project_id` bigint(20) NOT NULL,
          `displayorder` bigint(20) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");

    // Add milestones
    foreach ($milestones as $milestone) {
        // Completed
        if ($milestone['completed'] > 0) {
            $status = 2;
        }
        // Cancelled
        elseif ($milestone['cancelled'] > 0) {
            $status = 0;
        }
        // Active
        else {
            $status = 1;
        }

        $ms = new Milestone(array(
            'id'           => $milestone['id'],
            'name'         => $milestone['milestone'],
            'slug'         => $milestone['slug'],
            'codename'     => $milestone['codename'],
            'info'         => $milestone['info'],
            'changelog'    => $milestone['changelog'],
            'due'          => $milestone['due'] == 0 ? null : Time::date("Y-m-d H:i:s", $milestone['due']),
            'completed_on' => $milestone['completed'] == 0 ? null : Time::date("Y-m-d H:i:s", $milestone['completed']),
            'status'       => $status,
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

// Step 7
get('/step/7', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get projects
    $ticket_history = $db->select()->from('ticket_history')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
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
    ");

    // Add history
    foreach ($ticket_history as $history) {
        $changes = array();
        foreach (json_decode($history['changes'], true) as $change) {
            // Remove the 'mark' action
            if (array_key_exists('action', $change) and $change['action'] == 'mark') {
                unset($change['action']);
            }

            // Skip the 'open' change
            if (array_key_exists('action', $change) and $change['action'] == 'open') {
                continue;
            }

            $changes[] = $change;
        }

        // Create
        if (count($changes) or $history['comment'] != '') {
            $update = new TicketHistory(array(
                'id'         => $history['id'],
                'user_id'    => $history['user_id'],
                'ticket_id'  => $history['ticket_id'],
                'changes'    => json_encode($changes),
                'comment'    => $history['comment'],
                'created_at' => Time::date("Y-m-d H:i:s", $history['timestamp'])
            ));
            $update->save();
        }
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/8');
});

// Step 8
get('/step/8', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get projects
    $tickets = $db->select()->from('tickets')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
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
    ");

    // Add tickets
    foreach ($tickets as $ticket) {
        $t = new Ticket(array(
            'id'             => $ticket['id'],
            'ticket_id'      => $ticket['ticket_id'],
            'summary'        => $ticket['summary'],
            'body'           => $ticket['body'],
            'user_id'        => $ticket['user_id'],
            'project_id'     => $ticket['project_id'],
            'milestone_id'   => $ticket['milestone_id'],
            'version_id'     => $ticket['version_id'],
            'component_id'   => $ticket['component_id'],
            'type_id'        => $ticket['type'],
            'status_id'      => $ticket['status'],
            'priority_id'    => $ticket['priority'],
            'severity_id'    => $ticket['severity'],
            'assigned_to_id' => $ticket['assigned_to'],
            'is_closed'      => $ticket['closed'],
            'is_private'     => $ticket['private'],
            'votes'          => 0,
            'extra'          => $ticket['extra'],
            'created_at'     => Time::date("Y-m-d H:i:s", $ticket['created']),
            'updated_at'     => $ticket['updated'] ? Time::date("Y-m-d H:i:s", $ticket['updated']) : null
        ));
        $t->quick_save();
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/9');
});

// Step 9
get('/step/9', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get projects
    $updates = $db->select()->from('timeline')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
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
    ");

    // Add updates
    foreach ($updates as $update) {
        // Ticket action
        switch ($update['action']) {
            case 'open_ticket':
                $action = 'ticket_created';
                break;

            case 'close_ticket':
                $action = 'ticket_closed';
                break;

            case 'reopen_ticket':
                $action = 'ticket_reopened';
                break;

            default:
                $action = $update['action'];
        }

        // Get status id
        $status_id = null;
        $history = $db->select()->from('ticket_history')->custom_sql("WHERE `ticket_id`={$update['owner_id']} AND `created_at`='" . Time::date("Y-m-d H:i:s", $update['timestamp']) . "' LIMIT 1")->exec()->fetch();
        if ($history) {
            foreach (json_decode($history['changes'], true) as $change) {
                if (array_key_exists('action', $change) and $change['property'] == 'status') {
                    $status = $db->select()->from('ticket_status')->where('name', $change['to'])->exec()->fetch();
                    $status_id = $status['id'];
                }
            }
        }

        // Attempt to find the 'Closed' status id
        if ($status_id == null and $status = $db->select()->from('ticket_status')->where('name', 'Closed')->exec()->fetch()) {
            $status_id = $status['id'];
        }
        // Screw it, use the default Closed status id, '3'
        elseif ($status_id == null) {
            $status_id = 3;
        }

        // Create
        $timeline = new Timeline(array(
            'id'         => $update['id'],
            'project_id' => $update['project_id'],
            'owner_id'   => $update['owner_id'],
            'action'     => $action,
            'data'       => $status_id,
            'user_id'    => $update['user_id'],
            'created_at' => Time::date("Y-m-d H:i:s", $update['timestamp'])
        ));
        $timeline->save();
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/10');
});

// Step 10
get('/step/10', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get projects
    $usergroups = $db->select()->from('usergroups')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
        DROP TABLE IF EXISTS `traq_usergroups`;
        CREATE TABLE `traq_usergroups` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `is_admin` smallint(6) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");

    // Add usergroups
    foreach ($usergroups as $usergroup) {
        $group = new Group(array(
            'id'       => $usergroup['id'],
            'name'     => $usergroup['name'],
            'is_admin' => $usergroup['is_admin'],
        ));
        $group->save();
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/11');
});

// Step 11
get('/step/11', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get projects
    $users = $db->select()->from('users')->exec()->fetch_all();

    // Drop and recreate table
    run_query("
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
          `created_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");

    // Add users
    foreach ($users as $user) {
        $data = array(
            'id'           => $user['id'],
            'username'     => $user['username'],
            'password'     => $user['password'],
            'password_ver' => 'sha1',
            'name'         => $user['name'],
            'email'        => $user['email'],
            'group_id'     => $user['group_id'],
            'locale'       => 'enUS',
            'options'      => '{"watch_created_tickets":null}',
            'login_hash'   => sha1($user['id'] . $user['name'] . microtime() . $user['email'] . rand(0, 100)),
            'created_at'   => null
        );
        $db->insert($data)->into('users')->exec();
    }

    // Anonymous user
    $anon = new User(array(
        'username'   => 'Anonymous',
        'password'   => sha1(microtime() . rand(0, 200) . time() . rand(0, 200)) . microtime(),
        'name'       => 'Anonymous',
        'email'      => 'anonymous' . microtime() . '@' . $_SERVER['HTTP_HOST'],
        'group_id'   => 3,
        'locale'     => 'enUS',
        'options'    => '{"watch_created_tickets":null}',
        'login_hash' => sha1(microtime() . rand(0, 250) . time() . rand(0, 250) . microtime()),
    ));
    $anon->save();

    // Update tickets for anonymous user
    $db->update('tickets')->set(array('user_id' => $anon->id))->where('user_id', 0)->exec();
    $db->update('ticket_history')->set(array('user_id' => $anon->id))->where('user_id', 0)->exec();

    // Update timeline for anonymous user
    $db->update('timeline')->set(array('user_id' => $anon->id))->where('user_id', 0)->exec();

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/12');
});

// Step 12
get('/step/12', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Get priorities
    $priorities = $db->select()->from('priorities')->exec()->fetch_all();

    // Get defaults and flip them
    $defaults = array_slice($priorities, 0, 5);
    foreach ($defaults as $id => $data) {
        $defaults[$id]['old_id'] = $data['id'];

        switch ($defaults[$id]['id']) {
            case '1':
                $defaults[$id]['id'] = 5;
                break;

            case '2':
                $defaults[$id]['id'] = 4;
                break;

            case '4':
                $defaults[$id]['id'] = 2;
                break;

            case '5':
                $defaults[$id]['id'] = 1;
                break;
        }
    }

    // Merge together
    $priorities = array_merge(array_reverse($defaults), array_slice($priorities, 5));

    // Drop and recreate table
    run_query("
        DROP TABLE IF EXISTS `traq_priorities`;
        CREATE TABLE `traq_priorities` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");

    // Add priorities
    foreach ($priorities as $priority) {
        $p = new Priority(array(
            'id'   => $priority['id'],
            'name' => $priority['name'],
        ));
        $p->save();

        $db->update('tickets')->set(array('priority_id' => $priority['id']))->where('priority_id', $priority['old_id'])->exec();
    }

    // Next
    header("Location: " . Nanite::base_uri() . 'migrate.php?/step/13');
});

// Step 13
get('/step/13', function(){
    if (!array_key_exists('migrating', $_SESSION) or $_SESSION['migrating'] != true) {
        die("These are not the droids you are looking for.");
    }

    $db = get_connection();

    // Rename ticket_status to statuses
    run_query("
        DROP TABLE IF EXISTS `traq_statuses`;
        RENAME TABLE `traq_ticket_status` TO `traq_statuses`;
    ");

    // Rename ticket_types to types
    run_query("
        DROP TABLE IF EXISTS `traq_types`;
        RENAME TABLE `traq_ticket_types` TO `traq_types`;
    ");

    // Drop custom fields
    run_query("DROP TABLE IF EXISTS `traq_custom_fields`;");

    // Create permissions table
    run_query("
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
    ");

    // Insert permissions
    run_query("
        INSERT INTO `traq_permissions` (`id`, `project_id`, `type`, `type_id`, `action`, `value`)
        VALUES
          (1,0,'usergroup',0,'view',1),
          (2,0,'usergroup',0,'project_settings',0),
          (3,0,'usergroup',0,'create_tickets',1),
          (4,0,'usergroup',0,'update_tickets',1),
          (5,0,'usergroup',0,'delete_tickets',0),
          (6,0,'usergroup',0,'comment_on_tickets',1),
          (7,0,'usergroup',0,'edit_ticket_description',0),
          (8,0,'usergroup',0,'vote_on_tickets',1),
          (9,0,'usergroup',0,'add_attachments',1),
          (10,0,'usergroup',0,'view_attachments',1),
          (11,0,'usergroup',0,'delete_attachments',0),
          (12,0,'usergroup',0,'set_all_ticket_properties',0),
          (13,0,'usergroup',0,'edit_ticket_history',0),
          (14,0,'usergroup',0,'delete_ticket_history',0),
          (15,0,'usergroup',0,'create_wiki_page',0),
          (16,0,'usergroup',0,'edit_wiki_page',0),
          (17,0,'usergroup',0,'delete_wiki_page',0),
          (18,0,'usergroup',3,'create_tickets',0),
          (19,0,'usergroup',3,'comment_on_tickets',0),
          (20,0,'usergroup',3,'update_tickets',0),
          (21,0,'usergroup',3,'vote_on_tickets',0),
          (22,0,'usergroup',3,'add_attachments',0),
          (23,0,'role',0,'view',1),
          (24,0,'role',0,'project_settings',0),
          (25,0,'role',0,'create_tickets',1),
          (26,0,'role',0,'update_tickets',1),
          (27,0,'role',0,'delete_tickets',0),
          (28,0,'role',0,'comment_on_tickets',1),
          (29,0,'role',0,'edit_ticket_description',0),
          (30,0,'role',0,'vote_on_tickets',1),
          (31,0,'role',0,'add_attachments',1),
          (32,0,'role',0,'view_attachments',1),
          (33,0,'role',0,'delete_attachments',0),
          (34,0,'role',0,'set_all_ticket_properties',1),
          (35,0,'role',0,'edit_ticket_history',0),
          (36,0,'role',0,'delete_ticket_history',0),
          (37,0,'role',0,'create_wiki_page',0),
          (38,0,'role',0,'edit_wiki_page',0),
          (39,0,'role',0,'delete_wiki_page',0),
          (40,0,'role',1,'project_settings',1),
          (41,0,'role',1,'delete_tickets',1),
          (42,0,'role',1,'edit_ticket_description',1),
          (43,0,'role',1,'delete_attachments',1),
          (44,0,'role',1,'edit_ticket_history',1),
          (45,0,'role',1,'delete_ticket_history',1),
          (46,0,'role',1,'create_wiki_page',1),
          (47,0,'role',1,'edit_wiki_page',1),
          (48,0,'role',1,'delete_wiki_page',1);
    ");

    // Create project roles table
    run_query("
        DROP TABLE IF EXISTS `traq_project_roles`;
        CREATE TABLE `traq_project_roles` (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(255) DEFAULT NULL,
          `project_id` bigint(20) DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    // Insert project roles
    run_query("
        INSERT INTO `traq_project_roles` (`id`, `name`, `project_id`)
        VALUES
            (1,'Manager',0),
            (2,'Developer',0),
            (3,'Tester',0);
    ");

    // Create user-roles table
    run_query("
        DROP TABLE IF EXISTS `traq_user_roles`;
        CREATE TABLE `traq_user_roles` (
          `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(20) DEFAULT NULL,
          `project_id` int(20) DEFAULT NULL,
          `project_role_id` int(20) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    // Drop/create subscriptions table
    run_query("
        DROP TABLE IF EXISTS `traq_subscriptions`;
        CREATE TABLE `traq_subscriptions` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
          `user_id` bigint(20) NOT NULL,
          `project_id` bigint(20) NOT NULL,
          `object_id` bigint(20) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");

    // Drop/create plugins table
    run_query("
        DROP TABLE IF EXISTS `traq_plugins`;
        CREATE TABLE `traq_plugins` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `enabled` tinyint(1) NOT NULL DEFAULT '1',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    ");

    // Insert markdown plugin
    run_query("
        INSERT INTO `traq_plugins` (`id`, `file`, `enabled`)
        VALUES
            (1,'markdown',1);
    ");

    // Add settings
    $db->insert(array('setting' => 'date_format', 'value' => "d/m/Y"))->into('settings')->exec();
    $db->insert(array('setting' => 'notification_from_email', 'value' => "noreply@{$_SERVER['HTTP_HOST']}"))->into('settings')->exec();

    // Update settings
    $db->update('settings')->set(array('setting' => 'db_version', 'value' => '30000'))->where('setting', 'db_revision')->exec();
    $db->update('settings')->set(array('value' => 'enus'))->where('setting', 'locale')->exec();
    $db->update('settings')->set(array('value' => 'default'))->where('setting', 'theme')->exec();

    // Remove settings
    $db->delete()->from('settings')->where('setting', 'recaptcha_enabled')->exec();
    $db->delete()->from('settings')->where('setting', 'recaptcha_privkey')->exec();
    $db->delete()->from('settings')->where('setting', 'recaptcha_pubkey')->exec();
    $db->delete()->from('settings')->where('setting', 'seo_urls')->exec();

    // Next
    $_SESSION['migrating'] = false;
    header("Location: " . Nanite::base_uri() . 'migrate.php?/done');
});

// Done
get('/done', function(){
    View::set('title', 'Done');
    render('migrate/done');
});
