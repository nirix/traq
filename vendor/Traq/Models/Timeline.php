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

namespace Traq\Models;

use Radium\Database\Model;

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
    protected static $_table = 'timeline';

    /**
     * User relation.
     *
     * @return \Traq\Models\User
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * User relation.
     *
     * @return \Traq\Models\Project
     */
    public function project()
    {
        return $this->belongsTo('Project');
    }

    /**
     * Ticket relation.
     *
     * @return \Traq\Models\Ticket
     */
    public function ticket()
    {
        return Ticket::find($this->owner_id);
    }

    /**
     * Ticket status relation.
     *
     * @return \Traq\Models\Status
     */
    public function ticketStatus()
    {
        if (($this->data !== null or $this->data != '') and $this->action != 'ticket_updated') {
            return Status::find($this->data);
        } else {
            return $this->ticket()->status();
        }
    }

    /**
     * Milestone relation.
     *
     * @return \Traq\Models\Milestone
     */
    public function milestone()
    {
        return Milestone::find($this->owner_id);
    }

    /**
     * Wiki page relation.
     *
     * @return \Traq\Models\WikiPage
     */
    public function wikiPage()
    {
        return WikiPage::find($this->owner_id);
    }

    /**
     * URI for object.
     *
     * @return string
     */
    public function href($uri = null)
    {
        return $this->project()->href("timeline/{$this->id}" . ($uri ? "/{$uri}" : ''));
    }

    /**
     * If the row is a moved ticket event, returns the other project.
     *
     * @return object
     */
    public function otherProject() {
        if (!isset($this->_other_project)) {
            $this->_other_project = Project::find($this->data);
        }
        return $this->_other_project;
    }

    /**
     * Returns an array of timeline events.
     *
     * @since 3.1
     * @return array
     */
    public static function timelineEvents()
    {
        return array(
            'ticket_created',
            'ticket_closed',
            'ticket_reopened',
            'ticket_updated',
            'ticket_comment',
            'milestone_completed',
            'milestone_cancelled',
            'ticket_moved_from',
            'ticket_moved_to',
            'wiki_page_created',
            'wiki_page_edited'
        );
    }

    /**
     * Returns an array of timeline filters.
     *
     * @param string $filter
     *
     * @since 3.1
     * @return array
     */
    public static function timelineFilters($filter = null)
    {
        $filters = array(
            'new_tickets'           => array('ticket_created'),
            'tickets_opened_closed' => array('ticket_closed', 'ticket_reopened'),
            'ticket_updates'        => array('ticket_updated'),
            'ticket_comments'       => array('ticket_comment'),
            'ticket_moves'          => array('ticket_moved_from', 'ticket_moved_to'),
            'milestones'            => array('milestone_completed', 'milestone_cancelled'),
            'wiki_pages'            => array('wiki_page_created', 'wiki_page_edited')
        );

        // Return events for specific filter
        if ($filter) {
            return $filters[$filter];
        }

        return $filters;
    }
}
