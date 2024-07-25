<?php
/*!
 * Traq
 * Copyright (C) 2009-2022 Jack Polgar
 * Copyright (C) 2012-2022 Traq.io
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

use avalon\http\Request;
use traq\controllers\AppController;
use traq\helpers\Pagination;
use traq\models\Timeline;
use traq\models\User;

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

        $ticketIds = [];
        $milestoneIds = [];
        $wikiIds = [];
        $userIds = [];

        foreach ($events as $event) {
            $event = new Timeline($event, false);
            $createdOn = $event->getCreatedAt()->format('Y_m_d');
            $userId = $event->user_id;

            if (!\in_array($event->user_id, $userIds)) {
                $userIds[] = $event->user_id;
            }

            if (\in_array($event->action, Timeline::TICKET_EVENTS) && !\in_array($event->owner_id, $ticketIds)) {
                $ticketIds[] = $event->owner_id;
            } elseif (\in_array($event->action, Timeline::MILESTONE_EVENTS)) {
                $milestoneIds[] = $event->owner_id;
            } elseif (\in_array($event->action, Timeline::WIKI_EVENTS)) {
                $wikiIds[] = $event->owner_id;
            }

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

        $tickets = \count($ticketIds) ? $this->getTickets($ticketIds) : [];
        $users = \count($userIds) ? $this->getUsers($userIds) : [];
        $milestones = \count($milestoneIds) ? $this->getMilestones($milestoneIds) : [];
        $wikiPages = \count($wikiIds) ? $this->getWikiPages($wikiIds) : [];

        return $this->renderView('timeline/index', [
            'pagination' => $pagination,
            'groupedEvents' => $groupedEvents,
            'users' => $users,
            'tickets' => $tickets,
            'milestones' => $milestones,
            'wikiPages' => $wikiPages,
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
            return $this->show_no_permission();
        }

        $event = Timeline::find($eventId);
        $event->delete();

        if (!Request::isAjax()) {
            Request::redirectTo($this->project->href('timeline'));
        }

        return [
            'success' => true,
        ];
    }

    /**
     * Get the ticket info required for timeline events, not actual ticket models.
     */
    private function getTickets(array $ticketIds): array
    {
        $inIds = str_repeat('?, ', \count($ticketIds) - 1) . '?';
        $query = $this->db->prepare(
            "
            SELECT
                t.`id`,
                t.`ticket_id`,
                t.`summary` AS ticket_summary,
                s.`name` AS status_name
            FROM `{$this->db->prefix}tickets` t
            LEFT JOIN `{$this->db->prefix}statuses` s ON t.status_id = s.id
            WHERE t.`id` IN ($inIds)"
        );
        $query->exec($ticketIds);

        $tickets = [];
        foreach ($query->fetchAll() as $ticket) {
            $tickets[$ticket['id']] = $ticket;
        }

        return $tickets;
    }

    /**
     * Get user info for timeline events.
     */
    private function getUsers(array $userIds): array
    {
        $inIds = str_repeat('?, ', \count($userIds) - 1) . '?';
        $query = $this->db->prepare("SELECT `id`, `username` FROM `{$this->db->prefix}users` WHERE `id` IN ($inIds)");
        $query->exec($userIds);

        $users = [];
        foreach ($query->fetchAll() as $user) {
            $users[$user['id']] = new User($user, false);
        }

        return $users;
    }

    /**
     * Get milestone info for timeline events.
     */
    private function getMilestones(array $milestoneIds): array
    {
        $inIds = str_repeat('?, ', \count($milestoneIds) - 1) . '?';
        $query = $this->db->prepare("SELECT `id`, `name`, `slug` FROM `{$this->db->prefix}milestones` WHERE `id` IN ($inIds)");
        $query->exec($milestoneIds);

        $milestones = [];
        foreach ($query->fetchAll() as $milestone) {
            $milestones[$milestone['id']] = $milestone;
        }

        return $milestones;
    }

    /**
     * Get wiki page info for timeline events.
     */
    private function getWikiPages(array $wikiIds): array
    {
        $inIds = str_repeat('?, ', \count($wikiIds) - 1) . '?';
        $query = $this->db->prepare("SELECT `id`, `title`, `slug` FROM `{$this->db->prefix}wiki` WHERE `id` IN ($inIds)");
        $query->exec($wikiIds);

        $pages = [];
        foreach ($query->fetchAll() as $page) {
            $pages[$page['id']] = $page;
        }

        return $pages;
    }
}
