<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
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

namespace Traq\Installer\Controllers;

use Avalon\Database\ConnectionManager;
use Avalon\Database\Migrator;
use Traq\Models\User;
use Traq\Models\Group;
use Traq\Models\Setting;
use Traq\Models\Priority;
use Traq\Models\ProjectRole;
use Traq\Models\Status;
use Traq\Models\Severity;
use Traq\Models\Type;

/**
 * @author Jack P.
 * @since 4.0.0
 */
class Install extends AppController
{
    /**
     * Migrate database and create admin account.
     */
    public function installAction()
    {
        // Create database connection and load migrations
        $connection = ConnectionManager::create($_SESSION['db']);
        $this->loadMigrations();

        // Migrate the database.
        $m = new Migrator;
        $m->migrate('up');

        // Create admin account
        $admin = new User($_SESSION['admin'] + [
            'name'     => $_SESSION['admin']['username'],
            'group_id' => 1
        ]);
        $admin->save();

        // Set config file contents
        $this->set("config", $this->makeConfig());

        // Insert defaults
        $this->insertPriorities();
        $this->insertProjectRoles();
        $this->insertSettings();
        $this->insertSeverities();
        $this->insertStatuses();
        $this->insertTypes();
        $this->insertGroups();

        $this->title("Complete");
        return $this->render("complete.phtml");
    }

    /**
     * Create the configuration file contents.
     */
    protected function makeConfig()
    {
        $config = [
            "<?php",
            "return [",
            "    'environment' => \"production\",",
            "    'database' => [",
            "        'production' => [",
            "            'driver' => \"{$_SESSION['db']['driver']}\",",
            "            'host' => \"{$_SESSION['db']['host']}\",",
            "            'user' => \"{$_SESSION['db']['user']}\",",
            "            'password' => \"{$_SESSION['db']['password']}\",",
            "            'dbname' => \"{$_SESSION['db']['dbname']}\"",
            "        ]",
            "    ]",
            "];"
        ];

        return htmlentities(implode(PHP_EOL, $config));
    }

    /**
     * Creates the anonymous user and returns the ID.
     *
     * @return integer
     */
    protected function createAnonymousUser()
    {
        $user = new User([
            'name'     => "Anonymous",
            'username' => "Anonymous",
            'password' => rand(0, 9999) . time() . microtime(),
            'email'    => "noreply@" . $_SERVER['HTTP_HOST'],
            'group_id' => 3
        ]);
        $user->save();

        return $user->id;
    }

    /**
     * Insert priorities.
     */
    protected function insertPriorities()
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
    protected function insertProjectRoles()
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
    protected function insertSettings()
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
    protected function insertSeverities()
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
    protected function insertStatuses()
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
    protected function insertTypes()
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
    protected function insertGroups()
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
}
