<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

/**
 * Returns an array of timeline events.
 *
 * @since 3.1
 * @return array
 */
function timeline_events()
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
function timeline_filters($filter = null)
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
