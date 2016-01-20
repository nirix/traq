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
use Avalon\AppKernel;
use Avalon\Templating\View;
use Avalon\Database\ConnectionManager;
use Traq\Helpers\Notification;

/**
 * The heart of Traq.
 *
 * @package Traq
 * @author Jack P.
 */
class Kernel extends AppKernel
{
    protected static $loader;

    public function __construct()
    {
        global $autoloader;
        parent::__construct();

        static::$loader = $autoloader;

        require __DIR__ . '/version.php';

        // Connect to the database
        $db = $this->config['db'][$this->config['environment']];
        // $GLOBALS['db'] = DriverManager::getConnection([
        $GLOBALS['db'] = ConnectionManager::create([
            'dbname'   => $db['database'],
            'user'     => $db['username'],
            'password' => $db['password'],
            'host'     => $db['host'],
            'driver'   => $db['driver'],
            'prefix'   => $db['prefix']
        ]);
        define('PREFIX', $db['prefix']);
        unset($db);

        // Alias some commonly used classes
        class_alias('Avalon\\Templating\\View', 'View');
        class_alias('Avalon\\Http\\Request', 'Request');
        class_alias('Avalon\\Hook', 'Hook');

        class_alias('Traq\\Helpers\\Errors', 'Errors');
        class_alias('Traq\\Helpers\\Format', 'Format');
        class_alias('Traq\\Helpers\\Ticketlist', 'Ticketlist');
        class_alias('Traq\\Helpers\\TicketFilters', 'TicketFilters');

        class_alias('Avalon\\Helpers\\HTML', 'HTML');
        class_alias('Avalon\\Helpers\\Form', 'Form');
        class_alias('Avalon\\Helpers\\TWBS', 'TWBS');
        class_alias('Avalon\\Helpers\\Gravatar', 'Gravatar');

        // Load commonly used functions
        require __DIR__ . '/common.php';

        View::loadFunctions();

        // If a theme is set, prepend it's views directory
        if (setting('theme') !== 'default') {
            View::addPath(__DIR__ . '/../' . setting('theme') . '/views', true);
        }

        $this->loadTranslations();
        $this->loadPlugins();

        // Set mailer config
        if (isset($this->config['email'])) {
            Notification::setConfig($this->config['email']);
        }
    }

    protected function loadTranslations()
    {
        foreach (scandir("{$this->path}/translations") as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            require "{$this->path}/translations/{$file}";
        }
    }

    protected function loadPlugins()
    {
        global $autoloader;

        $queue = [];

        $plugins = queryBuilder()->select('*')->from(PREFIX . 'plugins')
            ->where('is_enabled = 1')
            ->execute()
            ->fetchAll();

        foreach ($plugins as $plugin) {
            $vendorDir = __DIR__ . '/../vendor';
            foreach (json_decode($plugin['autoload'], true) as $namespace => $directory) {
                $autoloader->addPsr4(
                    $namespace,
                    $vendorDir . "/{$plugin['directory']}/{$directory}"
                );
            }

            $class = $plugin['main'];
            if (class_exists($class)) {
                $class::init();
                $queue[] = $class;
            }
        }

        foreach ($queue as $plugin) {
            $plugin::enable();
        }
    }

    /**
     * Registers the namespace and directory with the autoloader.
     *
     * @param string $namespace
     * @param string $directory
     */
    public static function registerNamespace($namespace, $directory)
    {
        static::$loader->addPsr4($namespace, $directory);
    }

    // -------------------------------------------------------------------------
    // Overwritten methods

    /**
     * Load routes.
     */
    protected function loadRoutes()
    {
        if (file_exists("{$this->path}/config/routes.php")) {
            require "{$this->path}/config/routes.php";
        } else {
            throw new Exception("Unable to load routes file");
        }
    }

    /**
     * Load the environment configuration file.
     */
    protected function configureEnvironment()
    {
        if (isset($this->config['environment'])) {
            if (file_exists("{$this->path}/config/environment/{$this->config['environment']}.php")) {
                require "{$this->path}/config/environment/{$this->config['environment']}.php";
            }
        }
    }
}
