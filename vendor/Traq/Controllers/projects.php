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

namespace traq\controllers;

use avalon\http\Request;
use avalon\output\View;
use avalon\Database;

use traq\models\Ticket;
use traq\models\Timeline;
use traq\models\Milestone;
use traq\models\Type;
use traq\models\Status;
use traq\helpers\Pagination;

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
    public function action_index()
    {
        // No need to do anything here as the
        // AppController fetches the projects
        // for use with the project switcher.
    }

    /**
     * Handles the project info page.
     */
    public function action_view()
    {
        // Make sure this is a project
        if (!$this->project) {
            return $this->show_404();
        }

        // Get open and closed ticket counts.
        View::set('ticket_count', array(
            'open' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 0)->exec()->row_count(),
            'closed' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 1)->exec()->row_count()
        ));
    }

    /**
     * Handles the roadmap page.
     */
    public function action_roadmap($which = 'active')
    {
        // Get the projects milestones and send them to the view.
        $milestones = Milestone::select()->where('project_id', $this->project->id);

        // Are we displaying all milestones?
        if ($which == 'all') {
            // We do NOTHING!
        }
        // Just the completed ones?
        elseif ($which == 'completed') {
            $milestones = $milestones->where('status', 2);
        }
        // Just the cancelled ones?
        elseif ($which == 'cancelled') {
            $milestones = $milestones->where('status', 0);
        }
        // Looks like just the active ones
        else {
            $milestones = $milestones->where('status', 1);
        }

        // Get the milestones and send them to the view
        $milestones = $milestones->order_by('displayorder', 'ASC')->exec()->fetch_all();
        View::set('milestones', $milestones);
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
    public function action_timeline()
    {
        $rows = array();

        // Filters
        $filters = array_keys(timeline_filters());
        $events = timeline_events();

        // Check if filters are set
        if (isset(Request::$post['filters']) or isset($_SESSION['timeline_filters'])) {
            $filters = array();
            $events = array();

            // Fetch filters
            $timeline_filters = (isset(Request::$post['filters']) ? Request::$post['filters'] : $_SESSION['timeline_filters']);

            // Process filters
            foreach ($timeline_filters as $filter => $value) {
                $filters[] = $filter;
                $events = array_merge($events, timeline_filters($filter));
            }

            // Save filters to session
            $_SESSION['timeline_filters'] = $timeline_filters;
        }

        // Atom feed
        $this->feeds[] = array(Request::requestUri() . ".atom", l('x_timeline_feed', $this->project->name));

        // Fetch the different days with a nicely formatted
        // query for everyone to read easily, unlike the one
        // from 2.x and earlier, that were, well, you know,
        // completely ugly and looked hackish.
        $query = "
            SELECT
            DISTINCT
                YEAR(created_at) AS 'year',
                MONTH(created_at) AS 'month',
                DAY(created_at) AS 'day',
                created_at

            FROM " . $this->db->prefix . "timeline
            WHERE project_id = '{$this->project->id}'
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
            Database::connection()->query($query)->rowCount() // Row count
        );

        // Limit?
        if ($pagination->paginate) {
            $days_query = Database::connection()->query($query . " LIMIT {$pagination->limit}, " . $pagination->per_page);
        } else {
            $days_query = Database::connection()->query($query);
        }

        View::set(compact('pagination'));

        // Loop through the days and get their activity
        foreach ($days_query as $info) {
            // Construct the array for the day
            $day = array(
                'created_at' => $info['created_at'],
                'activity' => array()
            );

            // Get the date, without the time
            $date = explode(' ', $info['created_at']);
            $date = $date[0];

            // Fetch the activity for this day
            $fetch_activity = Timeline::select()
                ->where('project_id', $this->project->id)
                ->where('created_at', "{$date} %", "LIKE")
                ->custom_sql("AND `action` IN ('" . implode("','", $events) . "')")
                ->order_by('created_at', 'DESC');

            foreach ($fetch_activity->exec()->fetch_all() as $row) {
                // Push it to the days activity array.
                $day['activity'][] = $row;
            }

            // Push the days data to the
            // rows array,
            $rows[] = $day;
        }

        // Send the days and events to the view.
        View::set(array('days' => $rows, 'filters' => $filters, 'events' => $events));
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
