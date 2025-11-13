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

    public function getCreatedAt(): DateTime
    {
        return new DateTime($this->created_at);
    }

    public function getTranslationString(): string
    {
        return \sprintf(
            'timeline.%s',
            \str_replace('.', '_', $this->action)
        );
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
}
