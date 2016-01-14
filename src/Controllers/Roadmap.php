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

/**
 * Roadmap controller.
 *
 * @author Jack P.
 * @package Traq\Controllers
 * @since 4.0.0
 */
class Roadmap extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->title($this->translate('roadmap'));
    }

    /**
     * Roadmap index
     *
     * @param string $filter which milestones to display.
     */
    public function indexAction($filter = 'active')
    {
        list($openQuery, $startedQuery, $closedQuery) = $this->getTicketCountQueries();

        $query = queryBuilder()->select('*')
            ->addSelect("({$openQuery}) AS open_tickets")
            ->addSelect("({$startedQuery}) AS started_tickets")
            ->addSelect("({$closedQuery}) AS closed_tickets")
            ->from(PREFIX . 'milestones', 'm')
            ->where('project_id = ?');

        if ($filter == 'active') {
            $query->andWhere('status = 1');
        } elseif ($filter == 'completed') {
            $query->andWhere('status = 2');
        } elseif ($filter == 'cancelled') {
            $query->andWhere('status = 0');
        }

        $query->orderBy('display_order', 'ASC');
        $query->setParameter(0, $this->currentProject['id']);

        $milestones = $query->execute()->fetchAll();

        return $this->render('roadmap/index.phtml', ['milestones' => $milestones]);
    }

    /**
     * Milestone info page.
     *
     * @param string $mslug milestone slug
     */
    public function showAction($mslug)
    {
        list($openQuery, $startedQuery, $closedQuery) = $this->getTicketCountQueries();

        $milestone = queryBuilder()->select('*')->from(PREFIX . 'milestones', 'm')
            ->addSelect("({$openQuery}) AS open_tickets")
            ->addSelect("({$startedQuery}) AS started_tickets")
            ->addSelect("({$closedQuery}) AS closed_tickets")
            ->where('project_id = ?')
            ->andWhere('slug = ?')
            ->setParameter(0, $this->currentProject['id'])
            ->setParameter(1, $mslug);

        $milestone = $milestone->execute()->fetch();

        $this->title($milestone['name']);

        return $this->render('roadmap/show.phtml', ['milestone' => $milestone]);
    }

    /**
     * Returns an array containing the open, started and closed ticket count queries.
     *
     * @return array
     */
    protected function getTicketCountQueries()
    {
        // Open ticket count
        $openQuery = queryBuilder()->select('COUNT(ot.id)')
            ->from(PREFIX . 'tickets', 'ot')
            ->where('ot.milestone_id = m.id')
            ->andWhere('ot.is_closed = 0');

        // Started ticket count
        $startedQuery = queryBuilder()->select('COUNT(st.id)')
            ->from(PREFIX . 'tickets', 'st')
            ->where('st.milestone_id = m.id')
            ->andWhere('s.status = 2')
            ->leftJoin('st', PREFIX . 'statuses', 's', 's.id = st.status_id');

        // Closed query count
        $closedQuery = queryBuilder()->select('COUNT(ct.id)')
            ->from(PREFIX . 'tickets', 'ct')
            ->where('ct.milestone_id = m.id')
            ->andWhere('ct.is_closed = 1');

        return [$openQuery, $startedQuery, $closedQuery];
    }
}
