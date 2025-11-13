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
    // Before filters
    public $before = [
        // 'edit' => array('_check_permission'),
        'delete' => ['_check_permission']
    ];

    /**
     * Edit ticket update
     *
     * @param integer $id
     */
    public function edit(int $id)
    {
        // Get the ticket update
        $history = \Traq\Models\TicketHistory::find($id);

        if ($history->user_id !== $this->user->id && !$this->user->permission($this->project->id, "edit_ticket_history")) {
            // oh noes! display the no permission page.
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

        // View::set('history', $history);

        $this->render['layout'] = false;
        return $this->render('ticket_history/edit.overlay.phtml', [
            'history' => $history
        ]);
    }

    /**
     * Delete ticket update
     *
     * @param integer $id
     */
    public function delete(int $id)
    {
        // Get the ticket update
        $history = \Traq\Models\TicketHistory::find($id);

        // Delete the update
        $history->delete();

        return $this->redirectTo($history->ticket->href());
    }

    /**
     * Used to check the permission for the requested action.
     */
    public function _check_permission($action)
    {
        // Check if the user has permission
        if (!$this->user->permission($this->project->id, "{$action}_ticket_history")) {
            // oh noes! display the no permission page.
            $this->show_no_permission();
            return false;
        }
    }
}
