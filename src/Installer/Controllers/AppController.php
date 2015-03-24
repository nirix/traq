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

use PDOException;
use Doctrine\DBAL\DBALException;
use Avalon\Http\Controller;
use Avalon\Database\ConnectionManager;

/**
 * @author Jack P.
 * @since 4.0.0
 */
class AppController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->set("installStep", function($routeName) {
            return $this->request->basePath("index.php") . $this->generateUrl($routeName);
        });

        $this->set("drivers", [
            'pdo_mysql'  => "MySQL",
            'pdo_pgsql'  => "PostgreSQL",
            'pdo_sqlite' => "SQLite",
            // 'pdo_sqlsrv' => "SQL Server",
            // 'pdo_oci'    => "Oracle"
        ]);
    }

    /**
     * Set page title.
     *
     * @param string $title
     */
    protected function title($title)
    {
        $this->set("stepTitle", $title);
    }

    /**
     * Check the database form fields and connection.
     *
     * @access protected
     */
    protected function checkDatabaseInformation()
    {
        $this->title("Database Information");

        $errors = [];
        $driver = $this->request->post('driver');

        // Check fields
        if ($driver == "pdo_pgsql" || $driver == "pdo_mysql") {
            if (!$this->request->post('host')) {
                $errors[] = "Server is required";
            }

            if (!$this->request->post('user')) {
                $errors[] = "Username is required";
            }

            if (!$this->request->post('password')) {
                $errors[] = "Password is required";
            }

            if (!$this->request->post('dbname')) {
                $errors[] = "Database name is required";
            }
        } elseif ($driver == "pdo_sqlite") {
            if (!$this->request->post('path')) {
                $errors[] = "Database path is required";
            }
        }

        // Check connection
        if (!count($errors)) {
            $info = [
                'driver' => $driver,
            ];

            switch ($driver) {
                case "pdo_pgsql":
                case "pdo_mysql":
                    $info = $info + [
                        'host'     => $this->request->post('host'),
                        'user'     => $this->request->post('user'),
                        'password' => $this->request->post('password'),
                        'dbname'   => $this->request->post('dbname')
                    ];
                    break;

                case "pdo_sqlite":
                    $info['path'] = $this->request->post('path');
                    break;
            }

            try {
                // Lets try to do a few things with the database to confirm a connection.
                $db = ConnectionManager::create($info);
                $sm = $db->getSchemaManager();
                $sm->listTables();
            } catch (DBALException $e) {
                $errors[] = "Unable to connect to database: " . $e->getMessage();
            }
        }

        if (count($errors)) {
            $this->title("Database Information");
            return $this->render("steps/database_information.phtml", [
                'errors' => $errors
            ]);
        }

        $_SESSION['db'] = $info;
    }

    /**
     * Check admin account information.
     *
     * @access protected
     */
    protected function checkAccountInformation()
    {
        $errors = [];

        if (!$this->request->post('username')) {
            $errors[] = "Username is required";
        }

        if (!$this->request->post('password')) {
            $errors[] = "Password is required";
        }

        if (!$this->request->post('email')) {
            $errors[] = "Email is required";
        }

        if (count($errors)) {
            $this->title("Admin Account");
            return $this->render("steps/account_information.phtml", [
                'errors' => $errors
            ]);
        }

        $_SESSION['admin'] = [
            'username' => $this->request->post('username'),
            'password' => $this->request->post('password'),
            'email'    => $this->request->post('email')
        ];
    }
}
