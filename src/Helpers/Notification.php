<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
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

namespace Traq\Helpers;

use Avalon\Language;
use Avalon\Templating\View;
use Traq\Models\User;
use Traq\Models\Setting;
use Swift_Mailer;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Swift_Message;

/**
 * Notification email class.
 *
 * @package Traq\Helpers
 * @author Jack P.
 * @since 3.0.0
 */
class Notification
{
    protected static $config;
    protected static $initialised = false;
    protected static $mailer;
    protected static $mailerTransport;
    protected $message;

    /**
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($subject = null, $body = null, $contentType = null, $charset = null)
    {
        $this->setup();
        $this->message = Swift_Message::newInstance($subject, $body, $contentType, $charset);
        $this->message->setFrom(setting('notification_from_email'), setting('title'));
    }

    /**
     * Set mailer config.
     *
     * @param array $config
     */
    public static function setConfig(array $config)
    {
        static::$config = $config;
    }

    /**
     * Setup Swiftmailer.
     */
    public static function setup()
    {
        // Do nothing unless email config is set or it's already setup
        if (!static::$config || static::$initialised === false) {
            return false;
        }

        // Configure based on SMTP or Sendmail
        switch (static::$config['type']) {
            case "SMTP":
                $mailerTransport = Swift_SmtpTransport::newInstance(
                    static::$config['server'],
                    static::$config['port'],
                    (isset(static::$config['security']) ? static::$config['security'] : null)
                );

                $mailerTransport->setUsername(static::$config['username']);
                $mailerTransport->setPassword(static::$config['password']);
                break;

            case "sendmail":
                $mailerTransport = Swift_SendmailTransport::newInstance(static::$config['path']);
                break;
        }

        // Set the mailer
        static::$mailer = Swift_Mailer::newInstance($mailerTransport);
        static::$initialised = true;
    }

    /**
     * Set the to addresses of this message.
     *
     * @param mixed  $addresses
     * @param string $name
     *
     * @return Notification
     */
    public function setTo($addresses, $name = null)
    {
        $this->message->setTo($addresses, $name);
    }

    /**
     * Send the notification
     */
    public function send()
    {
        static::$mailer->send($this->message);
    }

    // -------------------------------------------------------------------------
    // Static Methods

    /**
     * @param \Swift_Mailer $mailer
     */
    public static function setMailer(Swift_Mailer $mailer)
    {
        static::$mailer = $mailer;
    }

    // -------------------------------------------------------------------------
    // Notification Types

    /**
     * Create a new account activation notification.
     *
     * @param  User $user
     *
     * @return Notification
     */
    public static function accountActivation(User $user, $activationCode)
    {
        $message = new static(
            Language::translate('notifications.account_activation.subject', ['title' => setting('title')]),
            View::render('notifications/account_activation.txt.php', [
                'user' => $user,
                'activationCode' => $activationCode
            ])
        );

        $message->setTo($user->email, $user->name);

        return $message;
    }
}
