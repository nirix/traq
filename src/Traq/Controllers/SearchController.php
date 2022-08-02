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
use traq\models\Milestone;
use traq\models\Project;
use traq\models\Ticket;

class SearchController extends AppController
{
    public function search()
    {
        $search = json_decode(Request::body(), true);

        if (!isset($search['query']) || $search['query'] === '') {
            return [
                'tickets' => [],
                'milestones' => [],
            ];
        }

        $term = "%{$search['query']}%";

        $tickets = Ticket::select()->where('summary', $term, 'LIKE');
        $milestones = Milestone::select()->where('name', $term, 'LIKE');

        // Filter by current project
        if (isset($search['project'])) {
            $project = Project::find('slug', $search['project']);
            $tickets->where('project_id', $project->id);
            $milestones->where('project_id', $project->id);
        } else {
            $projects = Project::select()->where('name', $term, 'LIKE');
        }

        $ticketData = array_map(
            function (Ticket $ticket) use ($project) {
                return [
                    'ticket_id' => $ticket->ticket_id,
                    'summary' => $ticket->summary,
                    'project' => isset($project)
                        ? ['name' => $project->name, 'slug' => $project->slug]
                        : ['name' => $ticket->project->name, 'slug' => $ticket->project->slug],
                ];
            },
            $tickets->exec()->fetchAll()
        );

        $milestoneData = array_map(
            function (Milestone $milestone) use ($project) {
                return [
                    'name' => $milestone->name,
                    'codename' => $milestone->codename ? $milestone->codename : null,
                    'slug' => $milestone->slug,
                    'project' => isset($project)
                        ? ['name' => $project->name, 'slug' => $project->slug]
                        : ['name' => $milestone->project->name, 'slug' => $milestone->project->slug],
                ];
            },
            $milestones->exec()->fetchAll()
        );

        return [
            'tickets' => $ticketData,
            'milestones' => $milestoneData,
        ];
    }
}
