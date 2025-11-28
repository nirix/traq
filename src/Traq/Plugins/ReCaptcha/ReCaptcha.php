<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

namespace Traq\Plugins;

use Avalon\Database;
use \FishHook;
use ReCaptcha\Controllers\ReCaptchaController;
use Request;
use Router;
use Traq\Libraries\Plugin;
use View;

/**
 * Security Questions Plugin.
 *
 * @package Traq
 * @subpackage Plugins
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class ReCaptcha extends Plugin
{
    protected static $info = [
        'name'    => 'reCaptcha',
        'version' => '1.0',
        'author'  => 'Jack P.'
    ];

    public static function init()
    {
        // Add routes
        Router::register('security_questions.index', '/admin/settings/recaptcha', [ReCaptchaController::class, 'index']);

        // Register the view path
        View::$searchPaths[] = dirname(__FILE__) . '/views';

        // Hook into the settings navbar
        FishHook::add('template:admin/settings/_nav', array(get_called_class(), 'adminNav'));

        if (settings('recaptcha_site_key') && settings('recaptcha_secret_key')) {
            // Hook into register form
            FishHook::add('template:users/register', array(get_called_class(), 'recaptchaField'));

            // Hook into the register action
            FishHook::add('controller:users.register', array(get_called_class(), 'verifyRecaptcha'));
        }
    }

    /**
     * Adds the link to the settings navbar.
     */
    public static function adminNav()
    {
        echo sprintf('<li%s><a href="%s">%s</a></li>', iif(active_nav('/admin/settings/recaptcha'), ' class="active"', ''), Request::base('/admin/settings/recaptcha'), l('recaptcha'));
    }

    /**
     * Adds the question field to the register form.
     */
    public static function recaptchaField()
    {
        echo View::render('users/recaptcha_field', ['siteKey' => settings('recaptcha_site_key')]);
    }

    /**
     * Checks the submitted answer.
     *
     * @param object $model
     */
    public static function verifyRecaptcha(&$model)
    {
        $recaptcha = new \ReCaptcha\ReCaptcha(settings('recaptcha_secret_key'));
        $resp = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
            ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess()) {
            $model->_add_error('recaptcha', l('errors.recaptcha.failed'));
        }
    }

    /**
     * Creates the setting row.
     */
    public static function __install()
    {
        Database::connection()->insert(array('setting' => 'recaptcha_site_key', 'value' => ''))->into('settings')->exec();
        Database::connection()->insert(array('setting' => 'recaptcha_secret_key', 'value' => ''))->into('settings')->exec();

        return true;
    }

    /**
     * Deletes the setting row.
     */
    public static function __uninstall()
    {
        Database::connection()->delete()->from('settings')->where('setting', 'recaptcha_site_key')->exec();
        Database::connection()->delete()->from('settings')->where('setting', 'recaptcha_secret_key')->exec();

        return true;
    }
}
