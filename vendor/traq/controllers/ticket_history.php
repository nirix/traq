<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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
use avalon\http\Router;
use avalon\output\View;
use traq\models\Ticket;
use traq\models\TicketHistory as TicketHistoryModel;

/**
 * Ticket History controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class TicketHistory extends AppController
{
    // Before filters
    public $before = array(
        'edit' => array('_load_history'),
        'delete' => array('_load_history', '_check_permission')
    );

    /**
     * History row for the ticket named in the URL.
     *
     * @var TicketHistoryModel|false
     */
    private $history = false;

    /**
     * Ticket that owns the history row.
     *
     * @var Ticket|false
     */
    private $ticket = false;

    /**
     * Edit ticket update
     *
     * @param integer $id
     */
    public function action_edit($id)
    {
        if (!$this->history || (int) $this->history->id !== (int) $id) {
            return $this->show404();
        }

        $isAuthor = (int) $this->history->user_id === (int) current_user()->id;
        if (!$isAuthor && !current_user()->permission($this->ticket->project_id, 'edit_ticket_history')) {
            $this->show_no_permission();
            return false;
        }

        // Has the form been submitted?
        if (Request::method() == 'post') {
            // Update the comment
            $this->history->set('comment', Request::$post['comment']);

            // Save and redirect
            if ($this->history->save()) {
                Request::redirectTo($this->ticket->href());
            }
        }

        View::set('history', $this->history);
    }

    /**
     * Delete ticket update
     *
     * @param integer $id
     */
    public function action_delete($id)
    {
        if (!$this->history || (int) $this->history->id !== (int) $id) {
            return $this->show404();
        }

        $this->history->delete();

        // Is this an ajax request?
        if (Request::isAjax()) {
            View::set('history', $this->history);
        } else {
            Request::redirectTo($this->ticket->href());
        }
    }

    /**
     * Load the history row only when it belongs to the ticket in this project.
     *
     * History ids are instance-wide. The ticket number in the URL is only
     * unique inside the project, so both have to match before any edit or delete.
     */
    public function _load_history()
    {
        if (!$this->render['action'] || !$this->project) {
            if (!$this->project && $this->render['action']) {
                $this->show404();
            }
            return;
        }

        if (!isset(Router::$params['ticket_id'], Router::$params['id'])) {
            $this->show404();
            return;
        }

        $ticketNumber = (string) Router::$params['ticket_id'];
        $historyId = (string) Router::$params['id'];

        if ($ticketNumber === '' || $historyId === '' || !ctype_digit($ticketNumber) || !ctype_digit($historyId)) {
            $this->show404();
            return;
        }

        $ticket = Ticket::select()
            ->where('ticket_id', (int) $ticketNumber)
            ->where('project_id', (int) $this->project->id)
            ->exec()
            ->fetch();

        if (!$ticket) {
            $this->show404();
            return;
        }

        if (!$this->canViewTicket($ticket)) {
            $this->show_no_permission();
            return;
        }

        $history = TicketHistoryModel::select()
            ->where('id', (int) $historyId)
            ->where('ticket_id', (int) $ticket->id)
            ->exec()
            ->fetch();

        if (!$history) {
            $this->show404();
            return;
        }

        $this->ticket = $ticket;
        $this->history = $history;
    }

    /**
     * Same visibility rule as the ticket page, including private tickets.
     */
    private function canViewTicket(Ticket $ticket)
    {
        if (!$ticket->is_private) {
            return true;
        }

        if ((int) $this->user->id === (int) $ticket->user_id) {
            return true;
        }

        if ($this->user->group->is_admin) {
            return true;
        }

        return $this->project->is_member($this->user);
    }

    /**
     * Used to check the permission for the requested action.
     */
    public function _check_permission($action)
    {
        if (!$this->render['action'] || !$this->history) {
            return;
        }

        if (!current_user()->permission($this->ticket->project_id, "{$action}_ticket_history")) {
            $this->show_no_permission();
            return false;
        }
    }
}
