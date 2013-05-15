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
require '../vendor/traq/helpers/uri.php';

use avalon\Database;
use avalon\output\View;

use traq\models\User;
use traq\models\Setting;
use traq\models\Ticket;

// Set page and title
View::set('page', 'upgrade');
View::set('page_title', 'Upgrade');

// Make sure the config file exists...
if (!file_exists('../vendor/traq/config/database.php')) {
    Error::halt('Error', 'Config file not found.');
}

// Get database connection
$db = get_connection();
define('DB_VER', Setting::find('db_version')->value);

// Index
get('/', function(){
    if (DB_VER < TRAQ_DB_VER) {
        render('upgrade/welcome');
    } else {
        render('upgrade/up_to_date');
    }
});

// Upgrade
post('/step/1', function(){
    global $db;

    // Version 3.0.6
    if (DB_VER < 30006) {
        // Find a user with the group ID of the guest group and make it the anonymous user.
        if ($anon = $db->select()->from('users')->where('group_id', 3)->exec()->fetch()) {
            $anon = new User($anon, false);
            $anon->set(array(
                'username'   => 'Anonymous',
                'password'   => sha1(microtime() . rand(0, 200) . time() . rand(0, 200)) . microtime(),
                'name'       => 'Anonymous',
                'email'      => 'anonymous' . microtime() . '@' . $_SERVER['HTTP_HOST'],
                'group_id'   => 3,
                'locale'     => 'enUS',
                'options'    => '{"watch_created_tickets":null}',
                'login_hash' => sha1(microtime() . rand(0, 250) . time() . rand(0, 250) . microtime()),
            ));
        }
        // Create an anonymous user
        else {
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
        }

        // Save anonymous user
        $anon->save();

        // Create setting to save anonymous user ID
        $anon_id = new Setting(array(
            'setting' => 'anonymous_user_id',
            'value'   => $anon->id
        ));
        $anon_id->save();

        // Update tickets, timeline, history with a user ID of -1
        // to use the anonymous users ID.
        $db->update('tickets')->set(array('user_id' => $anon->id))->where('user_id', -1)->exec();
        $db->update('ticket_history')->set(array('user_id' => $anon->id))->where('user_id', -1)->exec();

        // Update timeline for anonymous user
        $db->update('timeline')->set(array('user_id' => $anon->id))->where('user_id', -1)->exec();
    }

    // Version 3.0.7
    if (DB_VER < 30007) {
        foreach (Ticket::fetch_all() as $ticket) {
            $ticket->delete_voter('-1');
            $ticket->delete_voter(Setting::find('anonymous_user_id')->value);
            $ticket->quick_save();
        }

        // Fix severities table ID column to auto increment
        $db->query("
            ALTER TABLE `" . $db->prefix . "severities` CHANGE `id` `id` BIGINT(20)
             NOT NULL AUTO_INCREMENT
        ");
    }

    // Version 3.1
    if (DB_VER < 30100) {
        // Default value for project display order.
        $db->query("
            ALTER TABLE `" . $db->prefix . "projects` CHANGE `displayorder` `displayorder` BIGINT(20)
            NOT NULL DEFAULT '0'
        ");

        // Add api_key column to users table
        $db->query("
          ALTER TABLE `" . $db->prefix . "users` ADD COLUMN `api_key` VARCHAR(255)
          AFTER `login_hash`;
        ");

        // Add setting for registration/email validation
        $db->query("
            INSERT INTO `" . $db->prefix . "settings` (`setting`, `value`)
            VALUES
              ('email_validation',0);
        ");

        // Add permissions for moving tickets
        $db->query("
            INSERT INTO `" . $db->prefix . "permissions` (`project_id`, `type`, `type_id`, `action`, `value`)
            VALUES
              (0,'usergroup',0,'move_tickets',0),
              (0,'role',0,'move_tickets',0),
              (0,'role',1,'move_tickets',1);
        ");

        // Add permissions for ticket properties
        $db->query("
            INSERT INTO `" . $db->prefix . "permissions` (`project_id`, `type`, `type_id`, `action`, `value`)
            VALUES
              (0,'usergroup',0,'ticket_properties_set_assigned_to',0),
              (0,'usergroup',0,'ticket_properties_set_milestone',0),
              (0,'usergroup',0,'ticket_properties_set_version',0),
              (0,'usergroup',0,'ticket_properties_set_component',0),
              (0,'usergroup',0,'ticket_properties_set_severity',0),
              (0,'usergroup',0,'ticket_properties_set_priority',0),
              (0,'usergroup',0,'ticket_properties_set_status',0),
              (0,'usergroup',0,'ticket_properties_change_type',0),
              (0,'usergroup',0,'ticket_properties_change_assigned_to',0),
              (0,'usergroup',0,'ticket_properties_change_milestone',0),
              (0,'usergroup',0,'ticket_properties_change_version',0),
              (0,'usergroup',0,'ticket_properties_change_component',0),
              (0,'usergroup',0,'ticket_properties_change_severity',0),
              (0,'usergroup',0,'ticket_properties_change_priority',0),
              (0,'usergroup',0,'ticket_properties_change_status',0),
              (0,'usergroup',0,'ticket_properties_change_summary',0),
              (0,'role',0,'ticket_properties_set_assigned_to',1),
              (0,'role',0,'ticket_properties_set_milestone',1),
              (0,'role',0,'ticket_properties_set_version',1),
              (0,'role',0,'ticket_properties_set_component',1),
              (0,'role',0,'ticket_properties_set_severity',1),
              (0,'role',0,'ticket_properties_set_priority',1),
              (0,'role',0,'ticket_properties_set_status',1),
              (0,'role',0,'ticket_properties_change_type',1),
              (0,'role',0,'ticket_properties_change_assigned_to',1),
              (0,'role',0,'ticket_properties_change_milestone',1),
              (0,'role',0,'ticket_properties_change_version',1),
              (0,'role',0,'ticket_properties_change_component',1),
              (0,'role',0,'ticket_properties_change_severity',1),
              (0,'role',0,'ticket_properties_change_priority',1),
              (0,'role',0,'ticket_properties_change_status',1),
              (0,'role',0,'ticket_properties_change_summary',1);
        ");
    }

    // Version 3.2
    if (DB_VER < 30200) {
        // Add tasks column to tickets table
        $db->query("
            ALTER TABLE `" . $db->prefix . "tickets` ADD COLUMN `tasks` longtext
            AFTER `votes`;
        ");

        // Add new permissions
        $db->query("
            INSERT INTO `" . $db->prefix . "permissions` (`project_id`, `type`, `type_id`, `action`, `value`)
            VALUES
              (0,'usergroup',0,'delete_timeline_events',0),
              (0,'usergroup',0,'perform_mass_actions',0),
              (0,'usergroup',0,'ticket_properties_set_tasks',0),
              (0,'usergroup',0,'ticket_properties_change_tasks',0),
              (0,'usergroup',0,'ticket_properties_complete_tasks',0),
              (0,'role',0,'delete_timeline_events',0),
              (0,'role',1,'delete_timeline_events',1),
              (0,'role',0,'perform_mass_actions',0),
              (0,'role',1,'perform_mass_actions',1),
              (0,'role',0,'ticket_properties_set_tasks',1),
              (0,'role',0,'ticket_properties_change_tasks',1),
              (0,'role',0,'ticket_properties_complete_tasks',1);
          ");
    }

    if (DB_VER < 30201) {
        // Add default ticket type ID column to projects table
        $db->query("
            ALTER TABLE `". $db->prefix . "projects` ADD COLUMN `default_ticket_type_id` int
            AFTER `enable_wiki`;
        ");
    }

    // Update database version setting
    $db_ver = Setting::find('setting', 'db_version');
    $db_ver->value = TRAQ_VER_CODE;
    $db_ver->save();

    render('upgrade/complete');
});
