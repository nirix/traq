<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
 * https://github.com/nirix
 * https://traq.io
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

namespace Traq;

use Exception;

/**
 * Permissions API.
 *
 * @package Traq
 * @author Jack P.
 * @since 4.0.0
 */
class Permissions
{
    /**
     * Default permissions.
     *
     * @var array
     */
    protected static $defaults = [
        // Projects
        'projects' => [
            'view'                   => true,
            'project_settings'       => false,
            'delete_timeline_events' => false,
        ],

        // Tickets
        'tickets' => [
            'view_tickets'            => true,
            'create_tickets'          => false,
            'update_tickets'          => false,
            'delete_tickets'          => false,
            'move_tickets'            => false,
            'comment_on_tickets'      => false,
            'edit_ticket_description' => false,
            'vote_on_tickets'         => false,
            'add_attachments'         => false,
            'view_attachments'        => false,
            'delete_attachments'      => false,
            'perform_mass_actions'    => false,
        ],

        'ticket_properties' => [
            // Set ticket properties
            'ticket_properties_set_assigned_to'     => false,
            'ticket_properties_set_milestone'       => false,
            'ticket_properties_set_version'         => false,
            'ticket_properties_set_component'       => false,
            'ticket_properties_set_severity'        => false,
            'ticket_properties_set_priority'        => false,
            'ticket_properties_set_status'          => false,
            'ticket_properties_set_tasks'           => false,
            'ticket_properties_set_related_tickets' => false,

            // Change ticket properties
            'ticket_properties_change_type'            => false,
            'ticket_properties_change_assigned_to'     => false,
            'ticket_properties_change_milestone'       => false,
            'ticket_properties_change_version'         => false,
            'ticket_properties_change_component'       => false,
            'ticket_properties_change_severity'        => false,
            'ticket_properties_change_priority'        => false,
            'ticket_properties_change_status'          => false,
            'ticket_properties_change_summary'         => false,
            'ticket_properties_change_tasks'           => false,
            'ticket_properties_change_related_tickets' => false,
            'ticket_properties_complete_tasks'         => false,
        ],

        // Ticket history
        'ticket_history' => [
            'edit_ticket_history'   => false,
            'delete_ticket_history' => false,
        ],

        // Wiki page
        'wiki' => [
            'create_wiki_page' => false,
            'edit_wiki_page'   => false,
            'delete_wiki_page' => false
        ]
    ];

    /**
     * Added permissions.
     *
     * @var array
     */
    protected static $permissions = [];

    /**
     * Returns default permissions.
     *
     * @param boolean $withCategories
     *
     * @return array
     */
    public static function getDefaults($withCategories = false)
    {
        if ($withCategories) {
            $defaults = static::$defaults;
        } else {
            $defaults = [];

            foreach (static::$defaults as $category => $perms) {
                $defaults = $defaults + $perms;
            }
        }

        return $defaults;
    }

    /**
     * Returns default and added permissions.
     *
     * @return array
     */
    public static function getPermissions($withCategories = false)
    {
        $permissions = array_merge_recursive(static::getDefaults(true), static::$permissions);

        if ($withCategories) {
            return $permissions;
        } else {
            $flatPermissions = [];

            foreach (static::$defaults as $category => $perms) {
                $flatPermissions = $flatPermissions + $perms;
            }

            return $flatPermissions;
        }
    }

    /**
     * Add a permission.
     *
     * @param string  $action  Permission action
     * @param boolean $default Default value
     *
     * @throws Exception if permission exists
     */
    public static function add($action, $default = false, $category = 'misc')
    {
        $permissions = static::getPermissions();

        if (isset($permissions[$category][$action])) {
            throw new Exception("Permission [{$category}][{$action}] already exists.");
        }

        static::$permissions[$category][$action] = $default;
    }
}
