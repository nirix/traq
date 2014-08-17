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

use Radium\Application;
use Radium\Language;
use Radium\Action\View;

use Traq\Models\Setting;
use Traq\Models\Plugin;

class Traq extends Application
{
    protected static $version;

    public function __construct()
    {
        parent::__construct();

        // Start session
        session_start();

        // Include version file
        require __DIR__ . "/version.php";
        static::$version = TRAQ_VER;

        // Alias classes
        $this->aliasClasses();

        // Load default language
        require __DIR__ . "/Translations/enAU.php";
        Language::setCurrent(Setting::find('locale')->value);

        // Add theme to view search path.
        $theme = Setting::find('theme')->value;
        View::addSearchPath(__DIR__ . "/Views/{$theme}", true);

        // Add Twitter Bootstrap helper view directory to view search path.
        View::addSearchPath(__DIR__ . "/Views/TWBS");

        require __DIR__ . "/common.php";

        $this->loadPlugins();
    }

    protected function aliasClasses()
    {
        class_alias("Radium\Hook", "Hook");
        class_alias("Radium\Action\View", "View");

        // Radium helpers
        class_alias("Radium\Helpers\HTML", "HTML");
        class_alias("Radium\Helpers\Form", "Form");
        class_alias("Radium\Http\Request", "Request");

        // Traq helpers
        class_alias("Traq\Helpers\Format", "Format");
        class_alias("Traq\Helpers\Subscription", "Subscription");
        class_alias("Traq\Helpers\TWBS", "TWBS");
        class_alias("Traq\Helpers\Errors", "Errors");
        class_alias("Traq\Helpers\Gravatar", "Gravatar");
    }

    protected function loadPlugins()
    {
        $queue = array();

        $loader = require VENDORDIR . '/autoload.php';

        foreach (Plugin::allEnabled() as $plugin) {
            $class = "{$plugin->namespace}{$plugin->class}";

            // Register namespace with autoloader
            $loader->addPsr4($plugin->namespace, VENDORDIR . "/{$plugin->directory}");

            if (class_exists($class)) {
                $class::init();
                $queue[] = $class;
            }
        }

        foreach ($queue as $plugin) {
            $plugin::enable();
        }
    }

    public static function version()
    {
        return static::$version;
    }
}
