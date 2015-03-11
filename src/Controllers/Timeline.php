<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
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

use Avalon\Database;
use Radium\Http\Request;
use Radium\Http\Response;
use Radium\Helpers\Pagination;
use Radium\Helpers\Time;
use Traq\Models\Timeline as TimelineModel;

/**
 * Timeline controller.
 *
 * @author Jack P.
 */
class Timeline extends AppController
{
    /**
     * Handles the timeline page.
     */
    public function indexAction()
    {
        $days = [];

        // Filters
        $filters = array_keys(TimelineModel::timelineFilters());
        $events  = TimelineModel::timelineEvents();

        // Check if filters are set
        if (isset(Request::$post['filters']) or isset($_SESSION['timeline_filters'])) {
            $filters = [];
            $events  = [];

            // Fetch filters
            $timelineFilters = Request::post(
                'filters',
                isset($_SESSION['timeline_filters'])
                    ? $_SESSION['timeline_filters']
                    : []
            );

            // Process filters
            foreach ($timelineFilters as $filter => $value) {
                $filters[] = $filter;
                $events = array_merge($events, TimelineModel::timelineFilters($filter));
            }

            // Save filters to session
            $_SESSION['timeline_filters'] = $timelineFilters;
        }

        // Atom feed
        $this->feeds[] = [
            Request::pathInfo() . ".atom",
            $this->translate('x_timeline_feed', [$this->project->name])
        ];

        $query = TimelineModel::select()->where('project_id = ?', $this->project->id);
        $query->andWhere($query->expr()->in('action', $query->quote($events)))
            ->orderBy('created_at', 'DESC')
            ->addGroupBy("YEAR(created_at)")
            ->addGroupBy("MONTH(created_at)")
            ->addGroupBy("DAY(created_at)");

        // Pagination
        $pagination = new Pagination(
            (isset(Request::$request['page']) ? Request::$request['page'] : 1), // Page
            settings('timeline_days_per_page'), // Per page
            $query->rowCount()
        );

        // Limit?
        if ($pagination->paginate) {
            $query->limit($pagination->limit, $pagination->perPage);
        }

        $this->set(compact('pagination'));

        foreach ($query->fetchAll() as $day) {
            $activity = TimelineModel::select()->where('project_id = ?', $this->project->id);
            $activity->andWhere($activity->expr()->in('action', $query->quote($events)))
                ->orderBy('created_at', 'DESC')
                ->andWhere($activity->expr()->like(
                    'created_at',
                    $activity->getConnection()->quote(date("Y-m-d", Time::toUnix($day->created_at)) . '%')
                ));

            $days[] = [
                'created_at' => $day->created_at,
                'activity'   => $activity->fetchAll()
            ];
        }

        $this->set([
            'days'    => $days,
            'filters' => $filters,
            'events'  => $events
        ]);

        return $this->respondTo(function($format) {
            if ($format == 'html') {
                return $this->render('timeline/index.phtml');
            }
        });
    }

    /**
     * Delete timeline event.
     *
     * @param integer $event_id
     */
    public function deleteEventAction($event_id)
    {
        if (!$this->currentUser->permission($this->project->id, 'delete_timeline_events')) {
            return $this->showNoPermission();
        }

        $event = TimelineModel::find($event_id);
        $event->delete();

        return $this->respondTo(function($format) use($event) {
            if ($format == 'html') {
                return Request::redirectTo($this->project->href('timeline'));
            } elseif ($format == 'js') {
                return new Response(function($resp) use ($event) {
                    $resp->contentType = 'text/javascript';
                    $resp->body = $this->renderView('timeline/delete_event.js.php', [
                        '_layout' => false,
                        'event'   => $event
                    ]);
                });
            }
        });
    }
}
