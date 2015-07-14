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

namespace Traq;

use Exception;
use Avalon\AppKernel;
use Avalon\Language;
use Traq\Models\Setting;
use Traq\Models\Plugin;
use Traq\Helpers\Notification;
use Swift_SmtpTransport;
use Swift_SendmailTransport;
use Swift_Mailer;

/**
 * The heart of Traq.
 *
 * @author Jack Polgar <jack@polgar.id.au>
 * @package Traq
 */
class Traq extends AppKernel
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

    public function __construct()
    {
        // We'll need the autoloader instance
        static::$loader = require VENDORDIR . '/autoload.php';

        // This line looks weird without a comment
        parent::__construct();

        // Start session
        session_start();

        // Include version file
        require __DIR__ . "/version.php";
        static::$version = TRAQ_VERSION;

        // Alias classes
        $this->aliasClasses();

        // Load default language
        $this->setupLanguage();

        // Old common functions
        require __DIR__ . "/common.php";

        // And finally, load the plugins
        $this->loadPlugins();

        // Setup notifications
        $this->setupNotifications();
    }

    protected function aliasClasses()
    {
        class_alias("Avalon\\Hook", "Hook");
        class_alias("Avalon\\Templating\\View", "View");

        // Avalon helpers
        class_alias("Avalon\\Helpers\\HTML", "HTML");
        class_alias("Avalon\\Helpers\\Form", "Form");
        class_alias("Avalon\\Helpers\\Time", "Time");
        class_alias("Avalon\\Http\\Request", "Request");

        // Traq helpers
        class_alias("Traq\\Helpers\\Format", "Format");
        class_alias("Traq\\Helpers\\Subscription", "Subscription");
        class_alias("Traq\\Helpers\\TWBS", "TWBS");
        class_alias("Traq\\Helpers\\Errors", "Errors");
        class_alias("Traq\\Helpers\\Gravatar", "Gravatar");
        class_alias("Traq\\Helpers\\Ticketlist", "Ticketlist");
        class_alias("Traq\\Helpers\\TicketFilters", "TicketFilters");
    }

    /**
     * Loads and sets the language.
     */
    protected function setupLanguage()
    {
        require __DIR__ . "/translations/enAU.php";
        Language::setCurrent(Setting::get('locale')->value);
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
     * Setup Swiftmailer and notification class.
     */
    protected function setupNotifications()
    {
        // Do nothing unless email config is set
        if (!isset($this->config['email'])) {
            return false;
        }

        // Configure based on SMTP or Sendmail
        switch ($this->config['email']['type']) {
            case "SMTP":
                $this->mailerTransport = Swift_SmtpTransport::newInstance(
                    $this->config['email']['server'],
                    $this->config['email']['port'],
                    (isset($this->config['email']['security']) ? $this->config['email']['security'] : null)
                );

                $this->mailerTransport->setUsername($this->config['email']['username']);
                $this->mailerTransport->setPassword($this->config['email']['password']);
                break;

            case "sendmail":
                $this->mailerTransport = Swift_SendmailTransport::newInstance($this->config['email']['path']);
                break;
        }

        // Configure the mailer and Notification helper.
        $this->mailer = Swift_Mailer::newInstance($this->mailerTransport);
        Notification::setMailer($this->mailer);
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
     * @return string
     */
    public static function version()
    {
        return static::$version;
    }

    // =========================================================================
    // Overwritten functions

    /**
     * Loads the applications configuration.
     */
    protected function loadConfiguration()
    {
        $path = dirname($this->path) . "/config/config.php";

        if (file_exists($path)) {
            $this->config = require $path;

            if (isset($this->config['environment'])) {
                $_ENV['environment'] = $this->config['environment'];

                // Load environment
                $environemntPath = "{$this->path}/config/environment/{$_ENV['environment']}.php";
                if (file_exists($environemntPath)) {
                    require $environemntPath;
                }
            }
        } else {
            throw new Exception("Error loading configuration file: [{$path}]");
        }
    }

    /**
     * Setup templating.
     */
    protected function setupTemplating()
    {
        parent::setupTemplating();

        // Add theme to view search path.
        $theme = Setting::get('theme')->value;

        if ($theme !== 'default') {
            View::addPath(__DIR__ . "/../vendor/traq/themes/{$theme}", true);
        }
    }
}
