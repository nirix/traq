<?php
/*!
 * Traq
 *
 * Copyright (C) 2009-2019 Jack P.
 * Copyright (C) 2012-2019 Traq.io
 * https://github.com/nirix
 * https://traq.io
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq\Listeners;

use Traq\Events\TicketUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Traq\Ticket;
use Traq\TimelineEvent;

class TicketModification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TicketUpdated  $event
     * @return void
     */
    public function handle(TicketUpdated $event)
    {
        /** @var Ticket $ticket */
        $ticket = $event->getTicket();

        $timelineEvent = new TimelineEvent([
            'project_id' => $ticket->project_id,
            'user_id' => auth()->user()->id,
            'owner_type' => Ticket::class,
            'owner_id' => $ticket->id,
            'action' => $event->getAction(),
            'data' => $event->getData() + ['ticket_id' => $ticket->ticket_id],
        ]);

        $timelineEvent->save();
    }
}
