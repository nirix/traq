<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
 * Copyright (C) 2012-2015 Traq.io
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

        $issues = [];
        $query = queryBuilder()->select('summary', 'ticket_id', 'milestone_id', 'type_id')
            ->from(PREFIX . 'tickets')
            ->where('project_id = ?')
            ->orderBy('type_id', 'ASC')
            ->setParameter(0, $this->currentProject['id'])
            ->execute();

        foreach ($query->fetchAll() as $row) {
            $issues[$row['milestone_id']][] = $row;
        }

        $milestones = queryBuilder()->select('id', 'name', 'slug')
            ->from(PREFIX . 'milestones')
            ->where('project_id = ?')
            ->andWhere('status = 2')
            ->orderBy('display_order', 'DESC')
            ->setParameter(0, $this->currentProject['id'])
            ->execute()
            ->fetchAll();

        foreach ($milestones as $index => $milestone) {
            $milestones[$index]['changes'] = isset($issues[$milestone['id']]) ? $issues[$milestone['id']] : [];
        }

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
