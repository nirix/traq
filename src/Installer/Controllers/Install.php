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
use Traq\Database\Seeder;

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
        $seeder = new Seeder;
        $seeder->seed();

        // Remove database and account details from the session.
        unset($_SESSION['db'], $_SESSION['admin']);

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
            "    'db' => [",
            "        'production' => ["
        ];

        if ($_SESSION['db']['driver'] == "pdo_pgsql" || $_SESSION['db']['driver'] == "pdo_mysql") {
            $config[] = "            'driver' => \"{$_SESSION['db']['driver']}\",";
            $config[] = "            'host' => \"{$_SESSION['db']['host']}\",";
            $config[] = "            'user' => \"{$_SESSION['db']['user']}\",";
            $config[] = "            'password' => \"{$_SESSION['db']['password']}\",";
            $config[] = "            'dbname' => \"{$_SESSION['db']['dbname']}\",";
            $config[] = "            'prefix' => ''";
        } elseif ($_SESSION['db']['driver'] == "pdo_sqlite") {
            $config[] = "            'path' => \"{$_SESSION['db']['path']}\"";
        }

        $config[] = "        ]";
        $config[] = "    ]";
        $config[] = "];";

        return htmlentities(implode(PHP_EOL, $config));
    }
}
