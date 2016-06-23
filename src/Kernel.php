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
use Avalon\Templating\Engines\PhpExtended;
use Avalon\Database\ConnectionManager;
use Traq\Models\Plugin as PluginModel;

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
        static::$loader = $autoloader;

        session_start();

        parent::__construct();

        require_once __DIR__ . '/version.php';

        // Setup aliases
        $this->setupAliases();

        // Load translations
        $this->loadTranslations();

        // Load common functions
        require __DIR__ . '/common.php';

        // Load plugins
        $this->loadPlugins();
    }

    /**
     * Alias commonly used helpers.
     */
    protected function setupAliases()
    {
        class_alias('Avalon\\Templating\\View', 'View');
        class_alias('Avalon\\Http\\Request', 'Request');
        class_alias('Avalon\\Hook', 'Hook');

        // Avalon helpers
        class_alias('Avalon\\Helpers\\HTML', 'HTML');
        class_alias('Avalon\\Helpers\\Form', 'Form');
        class_alias('Avalon\\Helpers\\TWBS', 'TWBS');
        class_alias('Avalon\\Helpers\\Gravatar', 'Gravatar');

        // Traq helpers
        class_alias('Traq\\Helpers\\Format', 'Format');
        class_alias('Traq\\Helpers\\Timeline', 'Timeline');
        class_alias('Traq\\Helpers\\TicketFilters', 'TicketFilters');
        class_alias('Traq\\Helpers\\Ticketlist', 'Ticketlist');
    }

    /**
     * Load plugins.
     */
    protected function loadPlugins()
    {
        $queue = [];

        $plugins = PluginModel::select()
            ->where('is_enabled = ?')
            ->setParameter(0, true)
            ->execute()
            ->fetchAll();

        foreach ($plugins as $plugin) {
            $vendorDir = __DIR__ . '/../vendor';
            foreach (json_decode($plugin['autoload'], true) as $namespace => $directory) {
                static::$loader->addPsr4(
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
     * Load translations.
     */
    protected function loadTranslations()
    {
        foreach (scandir("{$this->path}/Translations") as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            require "{$this->path}/Translations/{$file}";
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
     * Connect to the database.
     */
    protected function configureDatabase()
    {
        $db = $this->config['db'][$this->config['environment']];

        if (!isset($db['prefix'])) {
            $db['prefix'] = '';
        }

        $GLOBALS['db'] = ConnectionManager::create($db);
        define('PREFIX', $db['prefix']);
        unset($db);
    }

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
     * Configure templating.
     */
    protected function configureTemplating()
    {
        $engine = new PhpExtended;
        $engine->escapeVariables = false;
        View::setEngine($engine);
        View::addPath("{$this->path}/views");

        View::loadFunctions();
    }
}
