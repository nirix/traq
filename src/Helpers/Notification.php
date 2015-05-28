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

namespace Traq\Helpers;

use Avalon\Language;
use Avalon\Templating\View;
use Traq\Models\User;
use Traq\Models\Setting;
use Swift_Mailer;
use Swift_Message;

/**
 * Notification email class.
 *
 * @author Jack Polgar <jack@polgar.id.au>
 * @since 3.0.0
 * @package Traq\Helpers
 */
class Notification
{
    /**
     * @var \Swift_Mailer
     */
    protected static $mailer;

    /**
     * @var \Swift_Message
     */
    protected $message;

    /**
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($subject = null, $body = null, $contentType = null, $charset = null)
    {
        $this->message = Swift_Message::newInstance($subject, $body, $contentType, $charset);
        $this->message->setFrom(Setting::get("notification_from_email")->value, Setting::get("title")->value);
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
    public static function accountActivation(User $user)
    {
        $message = new static(
            Language::translate("notifications.account_activation.subject", ['title' => Setting::get("title")->value]),
            View::render("notifications/account_activation.txt.php", ['user' => $user])
        );

        $message->setTo($user->email, $user->name);

        return $message;
    }
}
