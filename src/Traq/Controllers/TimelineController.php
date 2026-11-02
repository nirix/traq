<?php
/*!
 * Traq
 * Copyright (C) 2009-2026 Jack Polgar
 * Copyright (C) 2012-2026 Traq.io
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

namespace Traq\Controllers;

use Avalon\Http\Request;
use Avalon\Http\Response;
use Traq\Helpers\Pagination;
use Traq\Models\Timeline;

/**
 * @package Traq\Controllers
 * @since 3.8
 */
class TimelineController extends AppController
{
    public function index()
    {
        $page = Request::$request['page'] ?? 1;

        $rows = $this->db->select('*')
            ->from('timeline')
            ->where('project_id', $this->project->id)
            ->orderBy('created_at', 'DESC');

        $pagination = new Pagination(
            $page, // Page
            40, // Per page
            $rows->exec()->rowCount() // Row count
        );

        if ($pagination->paginate) {
            $rows->limit($pagination->limit, 40);
        }

        $events = $rows->exec()->fetchAll();

        $groupedEvents = [];
        $lastDate = null;
        $lastUserId = null;

        $timelineEvents = [];

        foreach ($events as $event) {
            $event = new Timeline($event, false);
            $timelineEvents[] = $event;
            $createdOn = $event->getCreatedAt()->format('Y_m_d');
            $userId = $event->user_id;

            // Create the day container
            if (!isset($groupedEvents[$createdOn])) {
                $groupedEvents[$createdOn] = [
                    'date' => $event->getCreatedAt(),
                    'events' => [],
                ];
            }

            $todaysEventsCount = \count($groupedEvents[$createdOn]['events']);
            $lastElement = $todaysEventsCount - 1;

            if ($todaysEventsCount === 0 || ($createdOn === $lastDate && $lastUserId !== $userId)) {
                // Append new user set.
                $groupedEvents[$createdOn]['events'][] = [
                    'user' => $event->user_id,
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

        return $this->render('timeline/index', [
            'pagination' => $pagination,
            'groupedEvents' => $groupedEvents,
            ...Timeline::getRelatedData($timelineEvents),
        ]);
    }

    /**
     * Delete timeline event.
     *
     * @param integer $eventId
     */
    public function deleteEvent($eventId)
    {
        if (!$this->user->permission($this->project->id, 'delete_timeline_events')) {
            return $this->json(['success' => false], Response::HTTP_FORBIDDEN);
        }

        $event = Timeline::select()
            ->where('id', (int) $eventId)
            ->where('project_id', (int) $this->project->id)
            ->exec()
            ->fetch();

        if (!$event) {
            return $this->show404();
        }

        $event->delete();

        if (!$this->isJson) {
            Request::redirectTo($this->project->href('timeline'));
        }

        return $this->json(['success' => true]);
    }
}
