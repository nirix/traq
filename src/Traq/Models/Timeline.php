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

namespace Traq\Models;

use Avalon\Database\Model;
use DateTime;

/**
 * Timeline model.
 *
 * @package Traq
 * @subpackage Models
 * @since 3.0
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Timeline extends Model
{
    protected static $_name = 'timeline';
    protected static $_properties = array(
        'id',
        'project_id',
        'owner_id',
        'action',
        'data',
        'user_id',
        'created_at'
    );

    protected static $_belongs_to = array('user');

    public const TICKET_EVENTS = [
        'ticket_created',
        'ticket_started',
        'ticket_updated',
        'ticket_closed',
        'ticket_reopened',
        'ticket_comment',
    ];

    public const TICKET_MOVED_EVENTS = [
        'ticket_moved_from',
        'ticket_moved_to',
    ];

    public const MILESTONE_EVENTS = [
        'milestone_completed',
        'milestone_cancelled',
    ];

    public const WIKI_EVENTS = [
        'wiki_page_created',
        'wiki_page_edited',
    ];

    /**
     * If the row is a ticket change, the ticket
     * object is returned.
     *
     * @return object
     */
    public function ticket()
    {
        if (!isset($this->_ticket)) {
            $this->_ticket = Ticket::find($this->owner_id);
        }
        return $this->_ticket;
    }

    /**
     * If the row is a milestone event, the milestone
     * object is returned.
     *
     * @return object
     */
    public function milestone()
    {
        if (!isset($this->_milestone)) {
            $this->_milestone = Milestone::find($this->owner_id);
        }
        return $this->_milestone;
    }

    public function is_valid()
    {
        return true;
    }

    /**
     * If the row is a ticket change, the new status
     * object is returned.
     *
     * @return object
     */
    public function ticket_status()
    {
        if (!isset($this->_ticket_status)) {
            $this->_ticket_status = Status::find($this->data);
        }
        return $this->_ticket_status;
    }

    /**
     * If the row is a moved ticket event, returns the other project.
     *
     * @return object
     */
    public function other_project()
    {
        if (!isset($this->_other_project)) {
            $this->_other_project = Project::find($this->data);
        }
        return $this->_other_project;
    }

    /**
     * If the row is a wiki page event, return the wiki page object.
     *
     * @return object
     */
    public function wiki_page()
    {
        if (!isset($this->_wiki_page)) {
            $this->_wiki_page = WikiPage::find($this->owner_id);
        }
        return $this->_wiki_page;
    }

    /**
     * Get the related rows needed to display the passed events.
     *
     * @param Timeline[] $events
     */
    public static function getRelatedData(array $events): array
    {
        $ticketIds = [];
        $milestoneIds = [];
        $wikiIds = [];
        $userIds = [];
        $projectIds = [];

        foreach ($events as $event) {
            $userIds[] = $event->user_id;

            if ($event->isTicket() || $event->isTicketMoved()) {
                $ticketIds[] = $event->owner_id;
            }

            if ($event->isTicketMoved()) {
                $projectIds[] = $event->data;
            } elseif ($event->isMilestone()) {
                $milestoneIds[] = $event->owner_id;
            } elseif ($event->isWiki()) {
                $wikiIds[] = $event->owner_id;
            }
        }

        return [
            'tickets' => static::fetchRelated(
                'SELECT t.`id`, t.`ticket_id`, t.`summary` AS ticket_summary, s.`name` AS status_name
                FROM `{prefix}tickets` t
                LEFT JOIN `{prefix}statuses` s ON t.status_id = s.id
                WHERE t.`id` IN ({ids})',
                $ticketIds
            ),
            'users' => array_map(
                fn ($user) => new User($user, false),
                static::fetchRelated('SELECT `id`, `username`, `name` FROM `{prefix}users` WHERE `id` IN ({ids})', $userIds)
            ),
            'milestones' => static::fetchRelated('SELECT `id`, `name`, `slug` FROM `{prefix}milestones` WHERE `id` IN ({ids})', $milestoneIds),
            'wikiPages' => static::fetchRelated('SELECT `id`, `title`, `slug` FROM `{prefix}wiki` WHERE `id` IN ({ids})', $wikiIds),
            'projects' => static::fetchRelated(
                'SELECT `id`, `name` AS project_name, `slug` AS project_slug FROM `{prefix}projects` WHERE `id` IN ({ids})',
                $projectIds
            ),
        ];
    }

    /**
     * Fetch rows by ID, keyed by ID.
     */
    private static function fetchRelated(string $sql, array $ids): array
    {
        if (!\count($ids)) {
            return [];
        }

        $ids = array_values(array_unique($ids));
        $placeholders = str_repeat('?, ', \count($ids) - 1) . '?';

        $query = static::db()->prepare(str_replace(['{prefix}', '{ids}'], [static::db()->prefix, $placeholders], $sql));
        $query->exec($ids);

        $rows = [];
        foreach ($query->fetchAll() as $row) {
            $rows[$row['id']] = $row;
        }

        return $rows;
    }

    public function getCreatedAt(): DateTime
    {
        return new DateTime($this->created_at);
    }

    public function isTicket(): bool
    {
        return \in_array($this->action, static::TICKET_EVENTS);
    }

    public function isMilestone(): bool
    {
        return \in_array($this->action, static::MILESTONE_EVENTS);
    }

    public function isWiki(): bool
    {
        return \in_array($this->action, static::WIKI_EVENTS);
    }

    public function isTicketMoved(): bool
    {
        return \in_array($this->action, static::TICKET_MOVED_EVENTS);
    }
}
