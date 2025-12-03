<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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
use Avalon\Output\View;
use Avalon\Http\Router;
use Traq\Models\Ticket;
use Traq\Models\Milestone;
use Traq\Models\Project;
use Traq\Models\Type;

/**
 * Project controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ProjectController extends AppController
{
    /**
     * Project listing page.
     */
    public function index()
    {
        $this->title(l('projects'));

        if ($this->isJson) {
            $projects = [];
            foreach (Project::fetchAll() as $project) {
                $projects[] = $project->__toArray();
            }
            return $this->json($projects);
        }

        return $this->render('projects/index.phtml');
    }

    /**
     * Handles the project info page.
     */
    public function view()
    {
        // Make sure this is a project
        if (!$this->project) {
            return $this->show404();
        }

        if (Router::$extension === '.json') {
            return $this->json($this->project->__toArray());
        }

        // Get open and closed ticket counts.
        View::set('ticket_count', array(
            'open' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 0)->exec()->row_count(),
            'closed' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 1)->exec()->row_count()
        ));

        return $this->render('projects/view.phtml');
    }

    /**
     * Handles the roadmap page.
     */
    public function roadmap(string $filter = 'active')
    {
        $this->title(l('roadmap'));

        $sortBy = explode('.', Request::get('sort', 'displayorder.asc'));
        $sort = strtolower($sortBy[0]);
        $sortOrder = strtoupper($sortBy[1]) === 'ASC' ? 'ASC' : 'DESC';

        // Determine sort field
        switch ($sort) {
            case 'due':
                $sortField = 'm.due';
                break;
            case 'name':
                $sortField = 'm.name';
                break;
            case 'display_order':
            default:
                $sortField = 'm.displayorder';
                break;
        }

        // Are we displaying all milestones?
        if ($filter == 'all') {
            $status = null;
        }
        // Just the completed ones?
        elseif ($filter == 'completed') {
            $status = 2;
        }
        // Just the cancelled ones?
        elseif ($filter == 'cancelled') {
            $status = 0;
        }
        // Looks like just the active ones
        else {
            $status = 1;
        }

        $milestoneData = Milestone::getDataForRoadmap($this->project->id, status: $status, sort: $sortField, order: $sortOrder);

        if (Router::$extension === '.json') {
            $data = [];
            foreach ($milestoneData as $milestone) {
                $data[] = $milestone['milestone']->__toArray();
            }

            return $this->json($data);
        }

        return $this->render('projects/roadmap.phtml', [
            'filter' => $filter,
            'sort' => $sort,
            'order' => $sortOrder === 'ASC' ? 'ASC' : 'DESC',
            'milestoneData' => $milestoneData,
        ]);
    }

    /**
     * Handles the milestone page.
     */
    public function viewMilestone($milestone_slug)
    {
        $milestoneData = Milestone::getDataForRoadmap($this->project->id, $milestone_slug);

        // Make sure milestone exists
        if (!$milestoneData || !$milestoneData['milestone']) {
            return $this->show404();
        }

        // Get the milestone
        $milestone = $milestoneData['milestone'];

        $this->title($milestone->name);

        if (Router::$extension === '.json') {
            return $this->json($milestone->__toArray());
        }

        // And send it to the view
        View::set('milestone', $milestone);

        return $this->render('projects/milestone.phtml', [
            'milestone' => $milestoneData['milestone'],
            'milestoneData' => $milestoneData,
        ]);
    }

    /**
     * Handles the changelog page.
     */
    public function changelog()
    {
        $this->title(l('changelog'));

        // Atom feed
        $this->feeds[] = array(Request::requestUri() . ".atom", l('x_changelog_feed', $this->project->name));

        // Fetch ticket types
        $types = array();
        foreach (Type::fetch_all() as $type) {
            $types[$type->id] = $type;
        }

        View::set('types', $types);
        View::set('milestones', $this->project->milestones->where('status', 2)->order_by('displayorder', 'DESC')->exec()->fetch_all());

        if (Router::$extension === '.atom') {
            return $this->render('projects/changelog.atom.php');
        }

        return $this->render('projects/changelog.phtml');
    }
}
