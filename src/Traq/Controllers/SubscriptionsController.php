<?php
/*!
 * Traq
 * Copyright (C) 2009-2022 Jack Polgar
 * Copyright (C) 2012-2022 Traq.io
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
class SubscriptionsController extends AppController
{
    /**
     * Unsubscribe via UUID
     */
    public function unsubscribe($uuid): Response
    {
        $sub = Subscription::findByUuid($uuid);

        if (!$sub) {
            return $this->redirectTo('/');
        }

        $sub->delete();

        return $this->render('subscriptions/unsubscribed.phtml', [
            'sub' => $sub,
        ]);
    }

    /**
     * Toggle project subscription.
     */
    public function toggleProject(string $project_slug): Response
    {
        // Delete subscription
        if (is_subscribed($this->user, $this->project)) {
            $sub = Subscription::select()->where([
                ['project_id', $this->project->id],
                ['user_id', $this->user->id],
                ['type', 'project']
            ])->exec()->fetch();
            $sub->delete();
        }
        // Create subscription
        else {
            $sub = new Subscription([
                'type'       => "project",
                'project_id' => $this->project->id,
                'user_id'    => $this->user->id,
                'object_id'  => $this->project->id
            ]);
            $sub->save();
        }

        return $this->redirectTo('/' . $this->project->href());
    }

    /**
     * Toggle milestone subscription
     */
    public function toggleMilestone(string $milestone_slug): Response
    {
        // Get milestone
        $milestone = Milestone::select()->where(array(
            ['project_id', $this->project->id],
            ['slug', $milestone_slug]
        ))->exec()->fetch();

        // Delete subscription
        if (is_subscribed($this->user, $milestone)) {
            $sub = Subscription::select()->where([
                ['project_id', $this->project->id],
                ['user_id', $this->user->id],
                ['type', 'milestone'],
                ['object_id', $milestone->id]
            ])->exec()->fetch();
            $sub->delete();
        } else {
            // Create subscription
            $sub = new Subscription([
                'type'       => "milestone",
                'project_id' => $this->project->id,
                'user_id'    => $this->user->id,
                'object_id'  => $milestone->id
            ]);
            $sub->save();
        }

        return $this->redirectTo($milestone->href());
    }

    /**
     * Toggle ticket subscription
     */
    public function toggleTicket(string $ticket_id): Response
    {
        // Get ticket
        $ticket = Ticket::select()->where([
            ['project_id', $this->project->id],
            ['ticket_id', $ticket_id]
        ])->exec()->fetch();

        // Delete subscription
        if (is_subscribed($this->user, $ticket)) {
            $sub = Subscription::select()->where([
                ['project_id', $this->project->id],
                ['user_id', $this->user->id],
                ['type', 'ticket'],
                ['object_id', $ticket->id]
            ])->exec()->fetch();
            $sub->delete();
        }
        // Create subscription
        else {
            $sub = new Subscription([
                'type'       => "ticket",
                'project_id' => $this->project->id,
                'user_id'    => $this->user->id,
                'object_id'  => $ticket->id
            ]);
            $sub->save();
        }

        return $this->redirectTo($ticket->href());
    }
}
