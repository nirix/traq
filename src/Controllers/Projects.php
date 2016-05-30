<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
 * https://github.com/nirix
 * https://traq.io
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

use Traq\Models\Project;
use Traq\Models\Status;
use Traq\Models\Type;

/**
 * Projects controller.
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 3.0.0
 */
class Projects extends AppController
{
    /**
     * Project listing.
     */
    public function indexAction()
    {
        $projects = Project::all();

        return $this->respondTo(function ($format) use ($projects) {
            if ($format == 'html') {
                return $this->render('projects/index.phtml', ['projects' => $projects]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($projects);
            }
        });
    }

    /**
     * Show project.
     *
     * @param string $slug
     */
    public function showAction($pslug)
    {
        $project = $GLOBALS['current_project'];

        return $this->respondTo(function ($format) use ($project) {
            if ($format == 'html') {
                return $this->render('projects/show.phtml', ['project' => $project]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($project);
            }
        });
    }

    /**
     * Project changelog.
     */
    public function changelogAction()
    {
        $this->addCrumb($this->translate('changelog'), $this->generateUrl('changelog'));

        $types = [];
        $milestones = [];

        // Get milestones
        $milestonesQuery = $this->currentProject->milestones()
            ->select('id', 'name', 'slug')
            ->where('status = 2')
            ->orderBy('display_order', 'DESC')
            ->fetchAll();

        foreach ($milestonesQuery as $milestone) {
            $milestones[$milestone['id']] = [
                'name' => $milestone['name'],
                'slug' => $milestone['slug'],
                'tickets' => []
            ];
        }

        // Get tickets
        if (count($milestones)) {
            $tickets = $this->currentProject->tickets()->select(
                't.ticket_id',
                't.summary',
                't.milestone_id',
                'status.id AS status_id',
                'status.name AS status_name',
                'status.show_on_changelog AS status_show_on_changelog',
                'type.id AS type_id',
                'type.name AS type_name',
                'type.bullet AS type_bullet',
                'type.show_on_changelog AS type_show_on_changelog'
            );

            // Left join the status and types values
            $tickets->leftJoin('t', Status::tableName(), 'status', 'status.id = t.status_id');
            $tickets->leftJoin('t', Type::tableName(), 'type', 'type.id = t.type_id');

            // Filter by closed and milestones
            $tickets->where('is_closed = 1')
                ->andWhere(
                    $tickets->expr()->in('milestone_id', array_keys($milestones))
                )
                ->orderBy('type_bullet', 'ASC');

            foreach ($tickets->fetchAll() as $ticket) {
                $ticketInfo = [
                    'ticket_id' => $ticket['ticket_id'],
                    'summary' => $ticket['summary'],
                    'type_id' => $ticket['type_id'],
                    'type_name' => $ticket['type_name'],
                    'status_id' => $ticket['status_id'],
                    'status_name' => $ticket['status_name']
                ];

                // Add types
                if (!isset($types[$ticket['type_id']])) {
                    $types[$ticket['type_id']] = [
                        'id' => $ticket['type_id'],
                        'name' => $ticket['type_name'],
                        'bullet' => $ticket['type_bullet']
                    ];
                }

                if ($ticket['status_show_on_changelog'] && $ticket['type_show_on_changelog']) {
                    $milestones[$ticket['milestone_id']]['tickets'][] = $ticketInfo;
                }
            }
        }

        // And now we get an array without the milestone ID's as the index.
        $milestones = array_values($milestones);

        return $this->respondTo(function ($format) use ($types, $milestones) {
            if ($format == 'html') {
                return $this->render('projects/changelog.phtml', [
                    'types' => $types,
                    'milestones' => $milestones
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($milestones);
            } elseif ($format == 'txt') {
                return $this->render('projects/changelog.txt.php', [
                    'types' => $types,
                    'milestones' => $milestones
                ]);
            }
        });
    }
}
