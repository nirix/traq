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

class Traq extends Application
{
    protected static $version;

    public function __construct()
    {
        parent::__construct();

        // Include version file
        require __DIR__ . "/version.php";
        static::$version = TRAQ_VER;

        // Alias classes
        $this->aliasClasses();

        // Load default language
        require __DIR__ . "/Translations/enAU.php";
        Language::setCurrent('enAU');

        // Add theme to view search path.
        $theme = Setting::find('theme')->value;
        View::addSearchPath(__DIR__ . "/Views/{$theme}", true);

        // Add Twitter Bootstrap helper view directory to view search path.
        View::addSearchPath(__DIR__ . "/Views/TWBS");

        require __DIR__ . "/common.php";
    }

    protected function aliasClasses()
    {
        class_alias("Radium\Hook", "Hook");

        // Radium helpers
        class_alias("Radium\Helpers\HTML", "HTML");
        class_alias("Radium\Helpers\Form", "Form");
        class_alias("Radium\Http\Request", "Request");

        // Traq helpers
        class_alias("Traq\Helpers\Format", "Format");
        class_alias("Traq\Helpers\Subscription", "Subscription");
        class_alias("Traq\Helpers\TWBS", "TWBS");
    }

    public static function version()
    {
        return static::$version;
    }
}
