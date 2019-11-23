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

namespace Traq\Database;

use Traq\Models\Group;
use Traq\Models\Priority;
use Traq\Models\Setting;
use Traq\Models\Severity;
use Traq\Models\Status;
use Traq\Models\Type;
use Traq\Models\ProjectRole;
use Traq\Models\User;
use Traq\Models\Permission;

/**
 * Contains all the default data for a clean database.
 *
 * @since 4.0
 * @package Traq\Database
 */
class Seeder
{
    /**
     * Seeds the database.
     */
    public function seed()
    {
        $this->insertGroups();
        $this->insertPriorities();
        $this->insertSettings();
        $this->insertSeverities();
        $this->insertStatuses();
        $this->insertTypes();
        $this->insertProjectRoles();
        $this->insertPermissions();
    }

    /**
     * Insert priorities.
     */
    public function insertPriorities()
    {
        $priorities = ['Highest', 'High', 'Normal', 'Low', 'Lowest'];

        foreach ($priorities as $priority) {
            $model = new Priority(['name' => $priority]);
            $model->save();
        }
    }

    /**
     * Insert project roles.
     */
    public function insertProjectRoles()
    {
        $roles = [
            ['name' => "Manager",   'is_assignable' => true, 'project_id' => 0],
            ['name' => "Developer", 'is_assignable' => true, 'project_id' => 0],
            ['name' => "Tester",    'is_assignable' => true, 'project_id' => 0]
        ];

        foreach ($roles as $role) {
            $model = new ProjectRole($role);
            $model->save();
        }
    }

    /**
     * Insert settings.
     */
    public function insertSettings()
    {
        $settings = [
            'title'                   => "Traq",
            'theme'                   => "default",
            'site_name'               => "",
            'site_url'                => "",
            'db_revision'             => \Traq\DB_REVISION,
            'locale'                  => "en_AU",
            'check_for_update'        => 1,
            'last_update_check'       => time(),
            'anonymous_user_id'       => $this->createAnonymousUser(),
            'allow_registration'      => 1,
            'email_validation'        => 0,
            'date_format'             => "d/m/Y",
            'date_time_format'        => "g:iA d/m/Y",
            'notification_from_email' => "noreply@" . $_SERVER['HTTP_HOST'],
            'ticket_creation_delay'   => 30,
            'ticket_history_sorting'  => "oldest_first",
            'tickets_per_page'        => 25,
            'timeline_day_format'     => "l, jS F Y",
            'timeline_days_per_page'  => 10,
            'timeline_time_format'    => "h:iA"
        ];

        foreach ($settings as $setting => $value) {
            $model = new Setting([
                'setting' => $setting,
                'value'   => $value
            ]);
            $model->save();
        }
    }

    /**
     * Insert severities.
     */
    public function insertSeverities()
    {
        $severities = ['Blocker', 'Critical', 'Major', 'Normal', 'Minor', 'Trivial'];

        foreach ($severities as $severity) {
            $model = new Severity(['name' => $severity]);
            $model->save();
        }
    }

    /**
     * Insert statuses.
     */
    public function insertStatuses()
    {
        $statuses = [
            ['name' => "New",       'status' => 1, 'show_on_changelog' => false],
            ['name' => "Accepted",  'status' => 1, 'show_on_changelog' => false],
            ['name' => "Started",   'status' => 2, 'show_on_changelog' => false],
            ['name' => "Closed",    'status' => 0, 'show_on_changelog' => true],
            ['name' => "Completed", 'status' => 0, 'show_on_changelog' => true]
        ];

        foreach ($statuses as $status) {
            $model = new Status($status);
            $model->save();
        }
    }

    /**
     * Insert types.
     */
    public function insertTypes()
    {
        $types = [
            ['name' => "Defect",          'bullet' => "-", 'show_on_changelog' => true],
            ['name' => "Feature Request", 'bullet' => "+", 'show_on_changelog' => true],
            ['name' => "Enhancement",     'bullet' => "*", 'show_on_changelog' => true],
            ['name' => "Task",            'bullet' => "*", 'show_on_changelog' => true],
        ];

        foreach ($types as $type) {
            $model = new Type($type);
            $model->save();
        }
    }

    /**
     * Insert user groups.
     */
    public function insertGroups()
    {
        $groups = [
            'Admin'   => true,
            'Members' => false,
            'Guests'  => false
        ];

        foreach ($groups as $name => $isAdmin) {
            $model = new Group([
                'name'     => $name,
                'is_admin' => $isAdmin
            ]);
            $model->save();
        }
    }

    /**
     * Insert permissions.
     */
    public function insertPermissions()
    {
        $permissions = [
            [
                'project_id' => 0,
                'type'       => 'usergroup',
                'type_id'    => 0,
                'permissions' => json_decode('{"view":true,"project_settings":false,"delete_timeline_events":false,"view_tickets":true,"create_tickets":true,"update_tickets":true,"delete_tickets":false,"move_tickets":false,"comment_on_tickets":true,"edit_ticket_description":false,"vote_on_tickets":true,"add_attachments":true,"view_attachments":true,"delete_attachments":false,"perform_mass_actions":false,"ticket_properties_set_assigned_to":false,"ticket_properties_set_milestone":true,"ticket_properties_set_version":true,"ticket_properties_set_component":false,"ticket_properties_set_severity":false,"ticket_properties_set_priority":false,"ticket_properties_set_status":false,"ticket_properties_set_tasks":false,"ticket_properties_set_related_tickets":false,"ticket_properties_change_type":false,"ticket_properties_change_assigned_to":false,"ticket_properties_change_milestone":false,"ticket_properties_change_version":false,"ticket_properties_change_component":true,"ticket_properties_change_severity":false,"ticket_properties_change_priority":false,"ticket_properties_change_status":false,"ticket_properties_change_summary":false,"ticket_properties_change_tasks":false,"ticket_properties_change_related_tickets":false,"ticket_properties_complete_tasks":false,"edit_ticket_history":false,"delete_ticket_history":false,"create_wiki_page":false,"edit_wiki_page":false,"delete_wiki_page":false}', true)
            ],
            [
                'project_id' => 0,
                'type'       => 'usergroup',
                'type_id'    => 3,
                'permissions' => json_decode('{"create_tickets":false,"comment_on_tickets":false,"update_tickets":false,"vote_on_tickets":false,"add_attachments":false}', true)
            ],
            [
                'project_id' => 0,
                'type'       => 'role',
                'type_id'    => 0,
                'permissions' => json_decode('{"view":true,"project_settings":false,"delete_timeline_events":false,"view_tickets":true,"create_tickets":true,"update_tickets":true,"delete_tickets":false,"move_tickets":false,"comment_on_tickets":true,"edit_ticket_description":false,"vote_on_tickets":true,"add_attachments":true,"view_attachments":true,"delete_attachments":false,"perform_mass_actions":false,"ticket_properties_set_assigned_to":true,"ticket_properties_set_milestone":true,"ticket_properties_set_version":true,"ticket_properties_set_component":true,"ticket_properties_set_severity":true,"ticket_properties_set_priority":true,"ticket_properties_set_status":true,"ticket_properties_set_tasks":true,"ticket_properties_set_related_tickets":true,"ticket_properties_change_type":true,"ticket_properties_change_assigned_to":true,"ticket_properties_change_milestone":true,"ticket_properties_change_version":true,"ticket_properties_change_component":true,"ticket_properties_change_severity":true,"ticket_properties_change_priority":true,"ticket_properties_change_status":true,"ticket_properties_change_summary":true,"ticket_properties_change_tasks":true,"ticket_properties_change_related_tickets":true,"ticket_properties_complete_tasks":true,"edit_ticket_history":false,"delete_ticket_history":false,"create_wiki_page":false,"edit_wiki_page":false,"delete_wiki_page":false}', true)
            ],
            [
                'project_id' => 0,
                'type'       => 'role',
                'type_id'    => 1,
                'permissions' => json_decode('{"project_settings":true,"delete_timeline_events":true,"delete_tickets":true,"move_tickets":true,"edit_ticket_description":true,"delete_attachments":true,"edit_ticket_history":true,"delete_ticket_history":true,"perform_mass_actions":true,"create_wiki_page":true,"edit_wiki_page":true,"delete_wiki_page":true}', true)
            ]
        ];

        foreach ($permissions as $permission) {
            $perm = new Permission($permission);
            $perm->save();
        }
    }

    /**
     * Creates the anonymous user and returns the ID.
     *
     * @return integer
     */
    protected function createAnonymousUser()
    {
        $password = rand(0, 9999) . time() . microtime();

        // For email validation, emails must match x@y.z
        $host = $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['SERVER_NAME'] == 'localhost'
            ? 'lvh.me'
            : explode(':', $_SERVER['HTTP_HOST'])[0];

        $host = is_array($host) ? $host[0] : $host;

        $user = new User([
            'name'                  => "Anonymous",
            'username'              => "Anonymous",
            'password'              => $password,
            'password_confirmation' => $password,
            'email'                 => "noreply@" . $host,
            'group_id'              => 3
        ]);

        if (!$user->save()) {
            $user->email = 'noreply@lvh.me';
            if (!$user->save()) {
                var_dump($user->email, $user->errors());
            }
        }

        return $user->id;
    }
}
