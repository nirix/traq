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

namespace Traq\Controllers;

use Radium\Http\Request;
use Radium\Http\Response;
use Radium\Helpers\Pagination;
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
        $rows = [];

        // Filters
        $filters = array_keys(TimelineModel::timelineFilters());
        $events  = TimelineModel::timelineEvents();

        // Check if filters are set
        if (isset(Request::$post['filters']) or isset($_SESSION['timeline_filters'])) {
            $filters = [];
            $events  = [];

            // Fetch filters
            $timelineFilters = (isset(Request::$post['filters']) ? Request::$post['filters'] : $_SESSION['timeline_filters']);

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
            $this->translate('x_timeline_feed', [$this->currentProject->name])
        ];

        // Fetch the different days with a nicely formatted
        // query for everyone to read easily, unlike the one
        // from 2.x and earlier, that was completely ugly.
        $query = "
            SELECT
            DISTINCT
                YEAR(created_at) AS 'year',
                MONTH(created_at) AS 'month',
                DAY(created_at) AS 'day',
                created_at

            FROM timeline
            WHERE `project_id` = '{$this->currentProject->id}'
            AND `action` IN ('" . implode("','", $events) . "')

            GROUP BY
                YEAR(created_at),
                MONTH(created_at),
                DAY(created_at)

            ORDER BY created_at DESC
        ";

        // Pagination
        $pagination = new Pagination(
            (isset(Request::$request['page']) ? Request::$request['page'] : 1), // Page
            settings('timeline_days_per_page'), // Per page
            $this->db->query($query)->rowCount() // Row count
        );

        // Limit?
        if ($pagination->paginate) {
            $daysQuery = $this->db->query($query . " LIMIT {$pagination->limit}, " . $pagination->perPage);
        } else {
            $daysQuery = $this->db->query($query);
        }

        $this->set(compact('pagination'));

        // Loop through the days and get their activity
        foreach ($daysQuery as $info) {
            // Construct the array for the day
            $day = [
                'created_at' => $info['created_at'],
                'activity' => []
            ];

            // Get the date, without the time
            $date = explode(' ', $info['created_at']);
            $date = $date[0];

            // Fetch the activity for this day
            $fetchActivity = TimelineModel::select()
                ->where('project_id = ?', $this->currentProject->id)
                ->andWhere("created_at LIKE '" . $date . " %'")
                ->andWhere("action IN ('".implode("','", $events)."')")
                ->orderBy('created_at', 'DESC');

            $day['activity'] = $fetchActivity->fetchAll();

            // Push the days data to the
            // rows array,
            $rows[] = $day;
        }

        // Send the days and events to the view.
        $this->set([
            'days'    => $rows,
            'filters' => $filters,
            'events'  => $events
        ]);

        return $this->respondTo(function($format){
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
        if (!$this->currentUser->permission($this->currentProject->id, 'delete_timeline_events')) {
            return $this->showNoPermission();
        }

        $event = TimelineModel::find($event_id);
        $event->delete();

        return $this->respondTo(function($format) use($event) {
            if ($format == 'html') {
                return Request::redirectTo($this->currentProject->href('timeline'));
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
