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

namespace traq\helpers;

use Avalon\Core\Error;
use Avalon\Http\Request;
use HTML;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use traq\models\User;
use traq\models\Subscription;

/**
 * Notification helper.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Helpers
 */
class Notification
{
    private static array $sent = [];
    private static ?TransportInterface $transport = null;
    private static ?MailerInterface $mailer = null;

    /**
     * Send a notification to a user, no checking.
     *
     * @param integer|object $user Users ID or model object
     * @param string         $type Event
     * @param array          $data Notification data
     */
    public static function send_to($user, $type, array $data)
    {
        if (!is_object($user)) {
            $user = User::find($user);
        }

        $data['type'] = $type;

        return static::notify($user, $data);
    }

    /**
     * Check if we should notify the user.
     *
     * @param string $type Event
     * @param object $sub  Subscription object
     * @param array  $data Notification data
     */
    private static function should_notify($type, $sub, $data)
    {
        switch ($type) {
            case 'ticket_created':
            case 'ticket_closed':
            case 'ticket_reopened':
                // Project
                if ($sub->type == 'project' and $data['ticket']->project_id == $sub->project_id) {
                    return true;
                }
                // Milestone
                elseif ($sub->type == 'milestone' and $data['ticket']->milestone_id == $sub->object_id) {
                    return true;
                }
                // Ticket
                elseif ($sub->type == 'ticket' and $data['ticket']->id == $sub->object_id) {
                    return true;
                }
                break;

            case 'ticket_updated':
                // Ticket
                if ($sub->type == 'ticket' and $data['ticket']->id == $sub->object_id) {
                    return true;
                }
                break;
        }
    }

    /**
     * Send notification for ticket.
     *
     * @param string $type   Event
     * @param object $ticket Ticket object
     */
    public static function send_for_ticket($type, $ticket)
    {
        // Set data
        $data['type'] = "ticket_{$type}";
        $data['ticket'] = $ticket;
        $data['project'] = $ticket->project;

        // Run subscriptions
        foreach (Subscription::fetch_all_for($data['project']->id) as $sub) {
            if (static::should_notify($data['type'], $sub, $data)) {
                static::notify($sub->user, $data, $sub);
            }
        }
    }

    /**
     * Notify the user.
     *
     * @param object $user    User object
     * @param array  $options Notification options
     * @param object $sub     Subscription object
     */
    private static function notify(User $user, array $options, Subscription $sub = null)
    {
        // Subject and message translation indexes
        $subject = "notifications.{$options['type']}.subject";
        $message = "notifications.{$options['type']}.message";

        switch ($options['type']) {
            // Ticket assigned to user
            // Ticket created
            // Ticket closed
            case 'ticket_assigned':
            case 'ticket_created':
            case 'ticket_updated':
            case 'ticket_reopened':
            case 'ticket_closed':
                // Subject
                $subject_vars = array(
                    settings('title'),
                    $options['ticket']->ticket_id,
                    $options['ticket']->summary,
                    $options['ticket']->project->name,
                );

                // Message
                $message_vars = array(
                    settings('title'),
                    $user->username,
                    $options['ticket']->ticket_id,
                    $options['ticket']->summary,
                    format_text($options['ticket']->body),
                    $options['ticket']->project->name,
                    $options['ticket']->project->slug,
                    "http://" . $_SERVER['HTTP_HOST'] . Request::base($options['ticket']->href())
                );
                break;

            // Email validation
            case 'email_validation':
                // Subject
                $subject_vars = array(
                    'username' => $user->username
                );

                // Message
                $message_vars = array(
                    'name'     => $user->name,
                    'username' => $user->username,
                    'link'     => $options['link']
                );
                break;
        }

        // Send notification
        if (!in_array($user->id, static::$sent)) {
            static::$sent[] = $user->id;
            return static::send($user, l($subject, $subject_vars), l($message, $message_vars), $sub);
        }
    }

    /**
     * Sends the email.
     *
     * @param object $user    User object
     * @param string $subject Email subject
     * @param string $message Email message
     * @param object $sub     Subscription object
     */
    public static function send(User $user, $subject, $message, Subscription $sub = null)
    {
        $mailer = static::getMailer();

        // Add unsubscribe link if this is a subscription
        if ($sub) {
            $port = in_array(Request::$port, [80, 443]) ? null : Request::$port;
            $host = $port ? Request::$host . ':' . $port : Request::$host;

            $unsubUrl = \sprintf(
                '%s://%s%s/unsubscribe/%s',
                Request::isSecure() || Request::$port === 443 ? 'https' : 'http',
                $host,
                trim(Request::base() ?? '', '/'),
                $sub->uuid
            );

            $message = \sprintf(
                '%s<br><br><hr><a href="%s">%s</a>',
                $message,
                $unsubUrl,
                l('unsubscribe')
            );
        }

        $email = (new Email())
            ->from(new Address(settings('notification_from_email'), settings('title')))
            ->to($user->email)
            ->subject($subject)
            ->html($message);

        try {
            $result = $mailer->send($email);
        } catch (\Exception $e) {
            Error::halt('Unable to send email', 'There was an error when attempting to send an email', $e->getMessage());
        }

        return $result;
    }

    private static function getTransportDsn(): string
    {
        $file = DATADIR . '/config/mailer.php';
        if (settings('mailer_config') === 'config' && file_exists($file)) {
            $config = require $file;
            return $config['dsn'];
        }

        return settings('mailer_dsn');
    }

    private static function getTransport(): TransportInterface
    {
        if (!static::$transport) {
            static::$transport = Transport::fromDsn(static::getTransportDsn());
        }

        return static::$transport;
    }

    public static function getMailer(): Mailer
    {
        if (!static::$mailer) {
            static::$mailer = new Mailer(static::getTransport());
        }

        return static::$mailer;
    }
}
