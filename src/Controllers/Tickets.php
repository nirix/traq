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

use Avalon\Http\Request;
use Traq\Models\Ticket;
use Traq\Models\Timeline;

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

    public function newAction()
    {
        $ticket = new Ticket([
            'type_id'     => $this->currentProject['default_ticket_type_id'],
            'severity_id' => 4
        ]);

        return $this->render('tickets/new.phtml', ['ticket' => $ticket]);
    }

    public function createAction()
    {
        $ticket = new Ticket($this->ticketParams());

        if ($ticket->validate()) {
            $ticket->save();

            Timeline::newTicketEvent($this->currentUser, $ticket)->save();

            $this->currentProject->next_ticket_id++;
            $this->currentProject->save();

            return $this->redirectTo('ticket', [
                'pslug' => $this->currentProject['slug'],
                'id'    => $ticket->ticket_id
            ]);
        }

        return $this->render('tickets/new.phtml', ['ticket' => $ticket]);
    }

    /**
     * Handles the view ticket page.
     *
     * @param integer $ticket_id
     */
    public function showAction($id)
    {
        $ticket = ticketQuery()
            ->addSelect('t.info')
            ->addSelect('t.is_closed')
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

    protected function ticketParams()
    {
        $params = [
            'ticket_id'    => $this->currentProject['next_ticket_id'],
            'summary'      => Request::$post->get('summary'),
            'info'         => Request::$post->get('info'),
            'user_id'      => $this->currentUser['id'],
            'project_id'   => $this->currentProject['id'],
            'milestone_id' => 0,
            'version_id'   => 0,
            'component_id' => 0,
            'type_id'      => Request::$post->get('type_id', $this->currentProject['default_ticket_type_id']),
            'severity_id'  => 4,
            'tasks'        => []
        ];

        return $this->ticketParamsPermissionable('set', $params);
    }

    /**
     * Get ticket data for the field the user is allowed to set or change.
     *
     * @param string $setOrChange set or change permission type
     * @param array  $params      already existing array of params
     *
     * @return array
     */
    protected function ticketParamsPermissionable($setOrChange, array $params = [])
    {
        // Milestone
        if ($this->hasPermission($this->currentProject['id'], "ticket_properties_{$setOrChange}_milestone")) {
            $params['milestone_id'] = Request::$post->get('milestone_id');
        }

        // Version
        if ($this->hasPermission($this->currentProject['id'], "ticket_properties_{$setOrChange}_version")) {
            $params['version_id'] = Request::$post->get('version_id');
        }

        // Component
        if ($this->hasPermission($this->currentProject['id'], "ticket_properties_{$setOrChange}_component")) {
            $params['component_id'] = Request::$post->get('component_id');
        }

        // Severity
        if ($this->hasPermission($this->currentProject['id'], "ticket_properties_{$setOrChange}_severity")) {
            $params['severity_id'] = Request::$post->get('severity_id');
        }

        // Priority
        if ($this->hasPermission($this->currentProject['id'], "ticket_properties_{$setOrChange}_priority")) {
            $params['priority_id'] = Request::$post->get('priority_id');
        }

        // Status
        if ($this->hasPermission($this->currentProject['id'], "ticket_properties_{$setOrChange}_status")) {
            $params['status_id'] = Request::$post->get('status_id');
        }

        // Assigned to
        if ($this->hasPermission($this->currentProject['id'], "ticket_properties_{$setOrChange}_assigned_to")) {
            $params['assigned_to_id'] = Request::$post->get('assigned_to_id');
        }

        // Tasks
        if ($this->hasPermission($this->currentProject['id'], "ticket_properties_{$setOrChange}_tasks")) {
            $tasks = json_decode(Request::$post->get('tasks', ''), true);

            if (is_array($tasks)) {
                foreach ($tasks as $id => $task) {
                    if (is_array($task) and !empty($task['task'])) {
                        $params['tasks'][] = $task;
                    }
                }
            }
        }

        return $params;
    }
}
