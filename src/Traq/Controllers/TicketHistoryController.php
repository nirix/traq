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
use Avalon\Http\Response;
use Traq\Models\Ticket;
use Traq\Models\TicketHistory;

/**
 * Ticket History controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class TicketHistoryController extends AppController
{
    /**
     * Edit ticket update.
     *
     * The history id is only valid for the ticket number in this project.
     */
    public function edit(int $ticket_id, int $id): Response
    {
        $history = $this->historyForTicket($ticket_id, $id);
        if ($history instanceof Response) {
            return $history;
        }

        $isAuthor = (int) $history->user_id === (int) $this->user->id;
        if (!$isAuthor && !$this->user->permission((int) $history->ticket->project_id, 'edit_ticket_history')) {
            return $this->renderNoPermission();
        }

        // Has the form been submitted?
        if (Request::method() == 'POST') {
            // Update the comment
            $history->set('comment', Request::$post['comment']);

            // Save and redirect
            if ($history->save()) {
                return $this->redirectTo($history->ticket->href());
            }
        }

        return $this->render('ticket_history/edit.overlay.phtml', [
            'history' => $history
        ]);
    }

    /**
     * Delete ticket update.
     *
     * The history id is only valid for the ticket number in this project.
     */
    public function delete(int $ticket_id, int $id): Response
    {
        $history = $this->historyForTicket($ticket_id, $id);
        if ($history instanceof Response) {
            return $history;
        }

        if (!$this->user->permission((int) $history->ticket->project_id, 'delete_ticket_history')) {
            return $this->renderNoPermission();
        }

        $history->delete();

        return $this->redirectTo($history->ticket->href());
    }

    /**
     * History ids are shared across the instance. Resolve the ticket number
     * inside this project first, then load the history row only for that ticket.
     */
    private function historyForTicket(int $ticketNumber, int $historyId): TicketHistory|Response
    {
        if (!$this->project) {
            return $this->show404();
        }

        $ticket = Ticket::select()
            ->where('ticket_id', $ticketNumber)
            ->where('project_id', (int) $this->project->id)
            ->exec()
            ->fetch();

        if (!$ticket || (int) $ticket->project_id !== (int) $this->project->id) {
            return $this->show404();
        }

        if (!$this->canViewTicket($ticket)) {
            return $this->renderNoPermission();
        }

        $history = TicketHistory::select()
            ->where('id', $historyId)
            ->where('ticket_id', (int) $ticket->id)
            ->exec()
            ->fetch();

        if (!$history || (int) $history->ticket_id !== (int) $ticket->id) {
            return $this->show404();
        }

        return $history;
    }

    /**
     * Same visibility rule as the ticket page, including private tickets.
     */
    private function canViewTicket(Ticket $ticket): bool
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
}
