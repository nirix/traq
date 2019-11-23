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

use Traq\Models\Milestone;
use Traq\Models\Ticket;
use Traq\Models\Status;

/**
 * Roadmap controller.
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 3.0.0
 */
class Roadmap extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->addCrumb($this->translate('roadmap'), $this->generateUrl('roadmap'));
    }

    /**
     * Milestone listing.
     */
    public function indexAction($filter = 'active')
    {
        list($openQuery, $startedQuery, $closedQuery) = $this->getTicketCountQueries();

        $query = Milestone::where('project_id = :project_id')
            ->addSelect("({$openQuery}) AS open_tickets")
            ->addSelect("({$startedQuery}) AS started_tickets")
            ->addSelect("({$closedQuery}) AS closed_tickets")
            ->setParameter('project_id', $this->currentProject['id'])
            ->setParameter('open_count_is_closed', false, 'boolean')
            ->setParameter('closed_count_is_closed', true, 'boolean')
            ->orderBy('display_order', 'ASC');

        if ($filter == 'active') {
            $query->andWhere('status = 1');
        } elseif ($filter == 'completed') {
            $query->andWhere('status = 2');
        } elseif ($filter == 'cancelled') {
            $query->andWhere('status = 0');
        }

        $milestones = $query->fetchAll();

        return $this->respondTo(function ($format) use ($milestones) {
            if ($format == 'html') {
                return $this->render('roadmap/index.phtml', ['milestones' => $milestones]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($milestones);
            }
        });
    }

    /**
     * Show milestone.
     *
     * @param string $slug
     */
    public function showAction($slug)
    {
        list($openQuery, $startedQuery, $closedQuery) = $this->getTicketCountQueries();

        $milestone = Milestone::where('project_id = :project_id')
            ->andWhere('slug = :slug')
            ->addSelect("({$openQuery}) AS open_tickets")
            ->addSelect("({$startedQuery}) AS started_tickets")
            ->addSelect("({$closedQuery}) AS closed_tickets")
            ->setParameter('project_id', $this->currentProject['id'])
            ->setParameter('slug', $slug)
            ->setParameter('open_count_is_closed', false, 'boolean')
            ->setParameter('closed_count_is_closed', false, 'boolean')
            ->fetch();

        $this->addCrumb($milestone['name'], $this->generateUrl('milestone', ['slug' => $milestone['slug']]));

        return $this->respondTo(function ($format) use ($milestone) {
            if ($format == 'html') {
                return $this->render('roadmap/show.phtml', ['milestone' => $milestone]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($milestone);
            }
        });
    }

    /**
     * Returns an array containing the open, started and closed ticket count queries.
     *
     * @return array
     */
    protected function getTicketCountQueries()
    {
        // Open ticket count
        $openQuery = Ticket::select('COUNT(t.id)')
            ->where('t.milestone_id = m.id')
            ->andWhere('t.is_closed = :open_count_is_closed');

        // Started ticket count
        $startedQuery = Ticket::select('COUNT(t.id)')
            ->where('t.milestone_id = m.id')
            ->andWhere('s.status = 2')
            ->leftJoin('t', Status::tableName(), 's', 's.id = t.status_id');

        // Closed query count
        $closedQuery = Ticket::select('COUNT(t.id)')
            ->where('t.milestone_id = m.id')
            ->andWhere('t.is_closed = :closed_count_is_closed');

        return [$openQuery, $startedQuery, $closedQuery];
    }
}
