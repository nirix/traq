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
use Radium\Action\View;
use Radium\Database;
use Radium\Helpers\Pagination;

use Traq\Models\Ticket;
use Traq\Models\Timeline;
use Traq\Models\Milestone;
use Traq\Models\Type;
use Traq\Models\Status;
use Traq\API;

/**
 * Project controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Projects extends AppController
{
    /**
     * Project listing page.
     */
    public function indexAction()
    {
        return  $this->respondTo(function($format, $controller){
            if ($format == 'json') {
                return API::response(200, $controller->render('Projects/index.json'));
            }
        });
    }

    /**
     * Handles the project info page.
     */
    public function showAction()
    {
        // Make sure this is a project
        if (!$this->project) {
            return $this->show_404();
        }

        // Get open and closed ticket counts.
        View::set('ticket_count', array(
            'open' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 0)->rowCount(),
            'closed' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 1)->rowCount()
        ));
    }

    /**
     * Handles the milestone page.
     */
    public function action_milestone($milestone_slug)
    {
        // Get the milestone
        $milestone = Milestone::select()->where(array(
            array('project_id', $this->project->id),
            array('slug', $milestone_slug)
        ))->exec()->fetch();

        // Make sure milestone exists
        if (!$milestone) {
            return $this->show_404();
        }

        // And send it to the view
        View::set('milestone', $milestone);
    }

    /**
     * Handles the changelog page.
     */
    public function action_changelog()
    {
        // Atom feed
        $this->feeds[] = array(Request::requestUri() . ".atom", l('x_changelog_feed', $this->project->name));

        // Fetch ticket types
        $types = array();
        foreach (Type::fetch_all() as $type) {
            $types[$type->id] = $type;
        }

        View::set('milestones', $this->project->milestones->where('status', 2)->order_by('displayorder', 'DESC')->exec()->fetch_all());
        View::set('types', $types);
    }

    /**
     * Handles the timeline page.
     */
    public function timelineAction()
    {
        $rows = array();

        // Filters
        $filters = array_keys(Timeline::timelineFilters());
        $events  = Timeline::timelineEvents();

        // Check if filters are set
        if (isset(Request::$post['filters']) or isset($_SESSION['timeline_filters'])) {
            $filters = array();
            $events  = array();

            // Fetch filters
            $timelineFilters = (isset(Request::$post['filters']) ? Request::$post['filters'] : $_SESSION['timeline_filters']);

            // Process filters
            foreach ($timelineFilters as $filter => $value) {
                $filters[] = $filter;
                $events = array_merge($events, Timeline::timelineFilters($filter));
            }

            // Save filters to session
            $_SESSION['timeline_filters'] = $timelineFilters;
        }

        // Atom feed
        $this->feeds[] = array(
            Request::$uri . ".atom",
            $this->translate('x_timeline_feed', array($this->project->name))
        );

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
            WHERE `project_id` = '{$this->project->id}'
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
            $day = array(
                'created_at' => $info['created_at'],
                'activity' => array()
            );

            // Get the date, without the time
            $date = explode(' ', $info['created_at']);
            $date = $date[0];

            // Fetch the activity for this day
            $fetchActivity = Timeline::select()
                ->where('project_id = ?', $this->project->id)
                ->_and("created_at LIKE '" . $date . " %'")
                ->_and("action IN ('".implode("','", $events)."')")
                ->orderBy('created_at', 'DESC');

            $day['activity'] = $fetchActivity->fetchAll();

            // Push the days data to the
            // rows array,
            $rows[] = $day;
        }

        // Send the days and events to the view.
        $this->set(array(
            'days'    => $rows,
            'filters' => $filters,
            'events'  => $events
        ));
    }

    /**
     * Delete timeline event.
     *
     * @param integer $event_id
     */
    public function action_delete_timeline_event($event_id)
    {
        if (!$this->user->permission($this->project->id, 'delete_timeline_events')) {
            return $this->show_no_permission();
        }

        $event = Timeline::find($event_id);
        $event->delete();

        if (!Request::isAjax()) {
            Request::redirectTo($this->project->href('timeline'));
        }

        View::set(compact('event'));
    }
}
