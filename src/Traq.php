<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
 * https://github.com/nirix
 * http://traq.io
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
use Radium\Application;
use Radium\Language;
use Radium\Templating\View;
use Avalon\Database\ConnectionManager;
use Traq\Models\Setting;
use Traq\Models\Plugin;

/**
 * The heart of Traq.
 *
 * @author Jack Polgar <jack@polgar.id.au>
 * @package Traq
 */
class Traq extends Application
{
    /**
     * @var string
     */
    protected static $version;

    /**
     * The composer autoloader instance.
     *
     * @var object
     */
    protected static $loader;

    /**
     * Path to user set configuration directory.
     *
     * @var string
     */
    protected $generalConfigDir;

    /**
     * Main configuration.
     *
     * @var array
     */
    protected $config;

    public function __construct()
    {
        // General configuration directory
        $this->generalConfigDir = __DIR__ . '/../config';

        // Load main configuration file
        $this->loadConfig();

        // We'll need the autoloader instance
        static::$loader = require VENDORDIR . '/autoload.php';

        // This line looks weird without a comment
        parent::__construct();

        // Start session
        session_start();

        // Include version file
        require __DIR__ . "/version.php";
        static::$version = TRAQ_VER;

        // Alias classes
        $this->aliasClasses();

        // Load default language
        require __DIR__ . "/translations/enAU.php";
        Language::setCurrent(Setting::get('locale')->value);

        // Add theme to view search path.
        $theme = Setting::get('theme')->value;

        if ($theme !== 'default') {
            View::addPath(__DIR__ . "/../vendor/traq/themes/{$theme}", true);
        }

        require __DIR__ . "/common.php";

        $this->loadPlugins();
    }

    /**
     * Alias classes for use in views.
     */
    protected function aliasClasses()
    {
        class_alias("Radium\Hook", "Hook");
        class_alias("Radium\Templating\View", "View");

        // Radium helpers
        class_alias("Radium\Helpers\HTML", "HTML");
        class_alias("Radium\Helpers\Form", "Form");
        class_alias("Radium\Helpers\Time", "Time");
        class_alias("Radium\Http\Request", "Request");

        // Traq helpers
        class_alias("Traq\Helpers\Format", "Format");
        class_alias("Traq\Helpers\Subscription", "Subscription");
        class_alias("Traq\Helpers\TWBS", "TWBS");
        class_alias("Traq\Helpers\Errors", "Errors");
        class_alias("Traq\Helpers\Gravatar", "Gravatar");
        class_alias("Traq\Helpers\Ticketlist", "Ticketlist");
        class_alias("Traq\Helpers\TicketFilters", "TicketFilters");
        class_alias("Traq\Helpers\Assets", "Assets");
    }

    /**
     * Load enabled plugins.
     */
    protected function loadPlugins()
    {
        $queue = array();

        foreach (Plugin::allEnabled() as $plugin) {
            $plugin->registerWithAutoloader();

            $class = $plugin->main;
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

    /**
     * Loads main configuration file.
     */
    protected function loadConfig()
    {
        $path = "{$this->generalConfigDir}/config.php";
        if (file_exists($path)) {
            $this->config = require $path;

            $this->environment = isset($this->config['environment']) ? $this->config['environment'] : 'dev';
        } else {
            throw new Exception("Unable to load main configuration file.");
        }
    }

    // -------------------------------------------------------------------------
    // Database related methods


    /**
     * Loads the database configuration file.
     */
    protected function loadDatabaseConfig()
    {
        $this->databaseConfigFile = "{$this->generalConfigDir}/database.php";

        if (file_exists($this->databaseConfigFile)) {
            $this->databaseConfig = require $this->databaseConfigFile;
        } else {
            parent::loadDatabaseConfig();
        }
    }

    /**
     * Connects to the configured database.
     */
    protected function connectDatabase()
    {
        if (!$this->databaseConfig) {
            return null;
        }

        $this->databaseConnection = ConnectionManager::create($this->databaseConfig);
    }

    /**
     * @return string
     */
    public static function version()
    {
        return static::$version;
    }
}
