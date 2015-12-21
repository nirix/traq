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
 * Ticket controller.
 *
 * @author Jack P.
 * @since 3.0.0
 */
class Tickets extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('tickets'));
    }

    /**
     * Handles the view ticket page.
     *
     * @param integer $ticket_id
     */
    public function showAction($id)
    {
        $ticket = ticketQuery()
            ->addSelect('t.body')
            ->where('t.project_id = ?')
            ->andWhere('t.ticket_id = ?')
            ->setParameter(0, $this->currentProject['id'])
            ->setParameter(1, $id)
            ->execute()
            ->fetch();

        $this->title($this->translate('ticket.page-title', $ticket['ticket_id'], $ticket['summary']));

        $history = queryBuilder()->select(
            'h.*',
            'u.name AS user_name'
        )
        ->from(PREFIX . 'ticket_history', 'h')
        ->where('h.ticket_id = :ticket_id')
        ->leftJoin('h', PREFIX . 'users', 'u', 'u.id = h.user_id')
        ->orderBy('h.created_at', 'ASC')
        ->setParameter('ticket_id', $ticket['id'])
        ->execute()
        ->fetchAll();

        return $this->render('tickets/show.phtml', [
            'ticket'  => $ticket,
            'history' => $history
        ]);
    }
}
