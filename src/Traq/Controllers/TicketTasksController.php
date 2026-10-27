<?php
/*!
 * Traq
 * Copyright (C) 2009-2026 Jack Polgar
 * Copyright (C) 2012-2026 Traq.io
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

use Avalon\Http\Response;
use Traq\Models\Ticket;

/**
 * TicketTasks controller
 *
 * @author Jack P.
 * @since 3.1
 * @package Traq
 * @subpackage Controllers
 */
class TicketTasksController extends AppController
{
    /**
     * Toggles the state of a task.
     *
     * @param integer $ticket_id
     * @param integer $task_id
     */
    public function toggle(int $ticket_id, int $task_id): Response
    {
        if ($this->user->permission($this->project->id, 'ticket_properties_complete_tasks')) {
            // Get ticket, update task and save
            $ticket = Ticket::select()->where('project_id', $this->project->id)->where('ticket_id', $ticket_id)->exec()->fetch();
            $ticket->toggle_task($task_id);
            $ticket->save();

            return $this->json([
                'success' => true,
            ]);
        }

        return $this->json([
            'success' => false,
        ]);
    }
}
