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

namespace traq\controllers;

use avalon\http\Request;
use avalon\output\View;
use avalon\Database;

use traq\models\Ticket;
use traq\models\Timeline;
use traq\models\Milestone;
use traq\models\Type;
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
        return $this->renderView('projects/index.phtml');
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
}
