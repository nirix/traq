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
use traq\models\Ticket;

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
     * Task manager.
     *
     * @param integer $ticket_id
     */
    public function manage(int $ticket_id): Response
    {
        $this->render['layout'] = false;
        return $this->render('ticket_tasks/manage.overlay.phtml', [
            'ticket_id' => $ticket_id,
        ]);
    }

    /**
     * Ticket task form bit.
     */
    public function formBit()
    {
        $this->render['layout'] = false;

        // Task data
        $id        = isset(Request::$request['id']) ? Request::$request['id'] : 0;
        $completed = isset(Request::$request['completed']) ? (Request::$request['completed'] == "true" ? true : false) : false;
        $task      = isset(Request::$request['task']) ? Request::$request['task'] : '';

        return $this->render('ticket_tasks/_form_bit', [
            'id' => $id,
            'completed' => $completed,
            'task' => $task,
        ]);
    }

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
