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

use Traq\Models\Ticket;

/**
 * Project controller.
 *
 * @author Jack P.
 * @since 3.0.0
 */
class Projects extends AppController
{
    /**
     * Project listing page.
     */
    public function indexAction()
    {
        $projects = queryBuilder()->select('*')
            ->from(PREFIX . 'projects')
            ->orderBy('display_order', 'ASC')
            ->execute()
            ->fetchAll();

        return $this->render('projects/index.phtml', [
            'projects' => $projects
        ]);
    }

    /**
     * Handles the project info page.
     */
    public function showAction()
    {
        return $this->render('projects/show.phtml', ['project' => $this->currentProject]);
    }

    /**
     * Handles the changelog page.
     */
    public function changelogAction()
    {
        $this->title($this->translate('changelog'));

        // Fetch issues
        $issues = [];
        $query = Ticket::select('t.summary', 't.ticket_id', 't.milestone_id', 't.type_id')
            ->leftJoin('t', PREFIX . 'types', 'type', 'type.id = t.type_id')
            ->leftJoin('t', PREFIX . 'statuses', 'status', 'status.id = t.status_id')
            ->where('t.project_id = ?')
            ->andWhere('type.show_on_changelog = 1')
            ->andWhere('status.show_on_changelog = 1')
            ->orderBy('t.type_id', 'ASC')
            ->setParameter(0, $this->currentProject['id'])
            ->execute();

        // Index issues by milestone ID
        foreach ($query->fetchAll() as $row) {
            $issues[$row['milestone_id']][] = $row;
        }

        // Fetch complete milestones
        $milestones = queryBuilder()->select('id', 'name', 'slug')
            ->from(PREFIX . 'milestones')
            ->where('project_id = ?')
            ->andWhere('status = 2')
            ->orderBy('display_order', 'DESC')
            ->setParameter(0, $this->currentProject['id'])
            ->execute()
            ->fetchAll();

        // Combine issues and milestones into a single array
        foreach ($milestones as $index => $milestone) {
            $milestones[$index]['changes'] = isset($issues[$milestone['id']]) ? $issues[$milestone['id']] : [];
        }

        // Fetch ticket types
        $types = [];
        $query = queryBuilder()->select('id', 'bullet')->from(PREFIX . 'types')->execute();
        foreach ($query->fetchAll() as $row) {
            $types[$row['id']] = $row['bullet'];
        }

        return $this->respondTo(function ($format) use ($milestones, $types) {
            if ($format == 'html') {
                return $this->render('projects/changelog.phtml', [
                    'milestones' => $milestones,
                    'types'      => $types
                ]);
            } elseif ($format == 'txt') {
                $resp = $this->render('projects/changelog.txt.php', [
                    '_layout'    => false,
                    'milestones' => $milestones
                ]);
                $resp->contentType = 'text/plain';
                return $resp;
            }
        });
    }
}
