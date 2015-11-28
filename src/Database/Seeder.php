<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
 * Copyright (C) 2012-2015 Traq.io
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
            'db_version'              => TRAQ_DB_VERSION_ID,
            'locale'                  => "enAU",
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
     * Creates the anonymous user and returns the ID.
     *
     * @return integer
     */
    protected function createAnonymousUser()
    {
        $password = rand(0, 9999) . time() . microtime();

        $user = new User([
            'name'                  => "Anonymous",
            'username'              => "Anonymous",
            'password'              => $password,
            'password_confirmation' => $password,
            'email'                 => "noreply@" . $_SERVER['HTTP_HOST'],
            'group_id'              => 3
        ]);
        $user->save();

        return $user->id;
    }
}
