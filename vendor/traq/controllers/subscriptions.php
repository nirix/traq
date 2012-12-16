<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

use traq\models\Subscription;
use traq\models\Milestone;
use traq\models\Ticket;

/**
 * Subscription controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Subscriptions extends AppController
{
    /**
     * Toggles the subscription.
     *
     * @param string  $type Subscription type (Project, Milestone, Ticket)
     * @param integer $id   Subscribed object ID
     */
    public function action_toggle($type, $id)
    {
        switch ($type) {
            // Project
            case 'project':
                // Delete subscription
                if (is_subscribed($this->user, $this->project)) {
                    $sub = Subscription::select()->where(array(
                        array('project_id', $this->project->id),
                        array('user_id', $this->user->id),
                        array('type', 'project')
                    ))->exec()->fetch();
                    $sub->delete();
                }
                // Create subscription
                else {
                    $sub = new Subscription(array(
                        'type'       => "project",
                        'project_id' => $this->project->id,
                        'user_id'    => $this->user->id,
                        'object_id'  => $this->project->id
                    ));
                    $sub->save();
                }
                Request::redirectTo($this->project->href());
                break;

            // Milestone
            case 'milestone':
                // Get milestone
                $milestone = Milestone::select()->where(array(
                    array('project_id', $this->project->id),
                    array('slug', $id)
                ))->exec()->fetch();

                // Delete subscription
                if (is_subscribed($this->user, $milestone)) {
                    $sub = Subscription::select()->where(array(
                        array('project_id', $this->project->id),
                        array('user_id', $this->user->id),
                        array('type', 'milestone'),
                        array('object_id', $milestone->id)
                    ))->exec()->fetch();
                    $sub->delete();
                }
                // Create subscription
                else {
                    $sub = new Subscription(array(
                        'type'       => "milestone",
                        'project_id' => $this->project->id,
                        'user_id'    => $this->user->id,
                        'object_id'  => $milestone->id
                    ));
                    $sub->save();
                }
                Request::redirectTo($milestone->href());
                break;

            // Milestone
            case 'ticket':
                // Get ticket
                $ticket = Ticket::select()->where(array(
                    array('project_id', $this->project->id),
                    array('ticket_id', $id)
                ))->exec()->fetch();

                // Delete subscription
                if (is_subscribed($this->user, $ticket)) {
                    $sub = Subscription::select()->where(array(
                        array('project_id', $this->project->id),
                        array('user_id', $this->user->id),
                        array('type', 'ticket'),
                        array('object_id', $ticket->id)
                    ))->exec()->fetch();
                    $sub->delete();
                }
                // Create subscription
                else {
                    $sub = new Subscription(array(
                        'type'       => "ticket",
                        'project_id' => $this->project->id,
                        'user_id'    => $this->user->id,
                        'object_id'  => $ticket->id
                    ));
                    $sub->save();
                }
                Request::redirectTo($ticket->href());
                break;
        }
    }
}
