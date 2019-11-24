<?php
/*!
 * Traq
 *
 * Copyright (C) 2009-2019 Jack P.
 * Copyright (C) 2012-2019 Traq.io
 * https://github.com/nirix
 * https://traq.io
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq\Http\Controllers\Project;

use Illuminate\Http\Request;
use Traq\Http\Controllers\Controller;
use Traq\Project;

class TimelineController extends Controller
{
    /**
     * @param Project $project
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Project $project)
    {
        $events = $project->timelineEvents()
            ->orderBy('created_at', 'DESC')
            ->with('user')
            ->paginate(50);

        // The timeline page should be laid out like so:
        // 27th July, 2019
        //   1:56pm - Updated ticket New Timeline (#3)
        //   1:52pm - Created ticket New Timeline (#3)
        //     by Picard
        //
        //   1:50pm - Created ticket ABC (#2)
        //     by Riker
        //
        //   1:49pm - Created ticket 123 (#1)
        //     by Picard
        $groupedEvents = [];
        $lastDate = null;
        $lastUserId = null;

        foreach ($events as $event) {
            $createdOn = $event->created_at->format('Y_m_d');
            $userId = $event->user_id;

            // Create the day container
            if (!isset($groupedEvents[$createdOn])) {
                $groupedEvents[$createdOn] = [
                    'date' => $event->created_at,
                    'events' => [],
                ];
            }

            $todaysEventsCount = \count($groupedEvents[$createdOn]['events']);
            $lastElement = $todaysEventsCount-1;

            if ($todaysEventsCount === 0 || ($createdOn === $lastDate && $lastUserId !== $userId)) {
                // Append new user set.
                $groupedEvents[$createdOn]['events'][] = [
                    'user' => $event->user,
                    'events' => [
                        $event,
                    ],
                ];
            } else {
                $groupedEvents[$createdOn]['events'][$lastElement]['events'][] = $event;
            }

            $lastDate = $createdOn;
            $lastUserId = $userId;
        }

        return view('timeline/index', [
            'project' => $project,
            'events' => $events,
            'groupedEvents' => $groupedEvents,
        ]);
    }
}
