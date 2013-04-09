<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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
use avalon\output\View;

use traq\models\Ticket;

/**
 * TicketTasks controller
 *
 * @author Jack P.
 * @since 3.1
 * @package Traq
 * @subpackage Controllers
 */
class TicketTasks extends AppController
{
    /**
     * Task manager.
     *
     * @param integer $ticket_id
     */
    public function action_manage($ticket_id)
    {
        // New ticket
        if ($ticket_id == 0) {
            $tasks = array(
                array('completed' => false, 'task' => '')
            );
        }
        // Existing ticket
        else {
            $ticket = Ticket::select()->where('project_id', $this->project->id)->where('ticket_id', $ticket_id)->exec()->fetch();
            if (!$ticket) {
                return $this->show_404();
            }
            $tasks = $ticket->tasks;
        }

        View::set('tasks', $tasks);
    }

    /**
     * Ticket task form bit.
     */
    public function action_form_bit()
    {
        $this->_render['layout'] = false;
        return View::render('ticket_tasks/_form_bit', array('id' => '', 'completed' => false, 'task' => ''));
    }
}
