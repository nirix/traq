<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

/**
 * Timeline model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Timeline extends Model
{
    public static function tableName($withPrefix = true)
    {
        return ($withPrefix ? static::connection()->prefix : '') . 'timeline';
    }

    /**
     * Creates a new Timeline object relating to a new ticket event.
     *
     * @return Timeline
     */
    public static function newTicketEvent($user, $ticket)
    {
        return new static([
            'project_id' => $ticket['project_id'],
            'owner_type' => 'Ticket',
            'owner_id'   => $ticket['id'],
            'user_id'    => $user['id'],
            'action'     => "ticket_created",
        ]);
    }

    /**
     * Creates a new Timeline object relating to an updated ticket event.
     *
     * @return Timeline
     */
    public static function updateTicketEvent($user, $ticket, $action, $status)
    {
        return new static([
            'project_id' => $ticket['project_id'],
            'owner_type' => 'Ticket',
            'owner_id'   => $ticket['id'],
            'user_id'    => $user['id'],
            'action'     => $action,
            'data'       => $status
        ]);
    }

    /**
     * Creates a new Timeline object relating to a milestone completion event.
     *
     * @return Timeline
     */
    public static function milestoneCompletedEvent($user, $milestone)
    {
        return new static([
            'project_id' => $milestone['project_id'],
            'owner_type' => 'Milestone',
            'owner_id'   => $milestone['id'],
            'user_id'    => $user['id'],
            'action'     => 'milestone_completed',
        ]);
    }

    /**
     * Creates a new Timeline object relating to a new wiki page event.
     *
     * @return Timeline
     */
    public static function wikiPageCreatedEvent($user, $page)
    {
        return new static([
            'project_id' => $page['project_id'],
            'owner_type' => 'WikiPage',
            'owner_id'   => $page['id'],
            'user_id'    => $user['id'],
            'action'     => "wiki_page_created",
        ]);
    }
}
