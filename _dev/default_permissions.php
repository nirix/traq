<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

// This file prints out queries to setup the default permissions.

header("Content-type: text/plain");
$queries = array();
$permission_id = 0;

$permissions = array(
    // Usergroup permissions
    'usergroup' => array(
        //------------------------------------------
        // Defaults

        // Projects
        array(0, 'view', 1),
        array(0, 'project_settings', 0),

        // Tickets
        array(0, 'create_tickets', 1),
        array(0, 'update_tickets', 1),
        array(0, 'delete_tickets', 0),
        array(0, 'comment_on_tickets', 1),
        array(0, 'edit_ticket_description', 0),
        array(0, 'vote_on_tickets', 1),
        array(0, 'add_attachments', 1),
        array(0, 'view_attachments', 1),
        array(0, 'delete_attachments', 0),
        array(0, 'set_all_ticket_properties', 0),

        // Ticket History
        array(0, 'edit_ticket_history', 0),
        array(0, 'delete_ticket_history', 0),

        // Wiki
        array(0, 'create_wiki_page', 0),
        array(0, 'edit_wiki_page', 0),
        array(0, 'delete_wiki_page', 0),

        //------------------------------------------
        // Guests

        // Tickets
        array(3, 'create_tickets', 0),
        array(3, 'comment_on_tickets', 0),
        array(3, 'update_tickets', 0),
        array(3, 'vote_on_tickets', 0),
        array(3, 'add_attachments', 0)
    ),
    // Role permissions
    'role' => array(
        //------------------------------------------
        // Defaults

        // Projects
        array(0, 'view', 1),
        array(0, 'project_settings', 0),

        // Tickets
        array(0, 'create_tickets', 1),
        array(0, 'update_tickets', 1),
        array(0, 'delete_tickets', 0),
        array(0, 'comment_on_tickets', 1),
        array(0, 'edit_ticket_description', 0),
        array(0, 'vote_on_tickets', 1),
        array(0, 'add_attachments', 1),
        array(0, 'view_attachments', 1),
        array(0, 'delete_attachments', 0),
        array(0, 'set_all_ticket_properties', 1),

        // Ticket History
        array(0, 'edit_ticket_history', 0),
        array(0, 'delete_ticket_history', 0),

        // Wiki
        array(0, 'create_wiki_page', 0),
        array(0, 'edit_wiki_page', 0),
        array(0, 'delete_wiki_page', 0),

        //------------------------------------------
        // Managers

        // Projects
        array(1, 'project_settings', 1),

        // Tickets
        array(1, 'delete_tickets', 1),
        array(1, 'edit_ticket_description', 1),
        array(1, 'delete_attachments', 1),
        array(1, 'edit_ticket_history', 1),
        array(1, 'delete_ticket_history', 1),

        // Wiki
        array(1, 'create_wiki_page', 1),
        array(1, 'edit_wiki_page', 1),
        array(1, 'delete_wiki_page', 1)
    )
);

// Permission types (usergroup/role)
foreach ($permissions as $type => $permissions) {
    $queries[] = "# {$type}";

    // Permissions
    foreach ($permissions as $permission) {
        $permission_id++;
        $queries[] = "INSERT INTO traq_permissions VALUES({$permission_id}, 0, '{$type}', {$permission[0]}, '{$permission[1]}', {$permission[2]});";
    }
}

print(implode(PHP_EOL, $queries));
