<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

class Timeline
{
    /**
     * Get an array of allowed timeline events.
     *
     * @return array
     */
    public static function events()
    {
        return [
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
        ];
    }

    /**
     * Returns an array of timeline filters.
     *
     * @param string $filter
     *
     * @since 3.1.0
     * @return array
     */
    public static function filters($filter = null)
    {
        $filters = [
            'new_tickets'           => ['ticket_created'],
            'tickets_opened_closed' => ['ticket_closed', 'ticket_reopened'],
            'ticket_updates'        => ['ticket_updated'],
            'ticket_comments'       => ['ticket_comment'],
            'ticket_moves'          => ['ticket_moved_from', 'ticket_moved_to'],
            'milestones'            => ['milestone_completed', 'milestone_cancelled'],
            'wiki_pages'            => ['wiki_page_created', 'wiki_page_edited']
        ];

        // Return events for specific filter
        if ($filter) {
            return $filters[$filter];
        }

        return $filters;
    }
}
