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

use Avalon\Core\Load;
use Avalon\Http\Response;
use Traq\Models\User;

class ProfileController extends AppController
{
    /**
     * User profile page.
     */
    public function view(int $id): Response
    {
        // If the user doesn't exist
        // display the 404 page.
        if (!$user = User::find($id)) {
            return $this->show404();
        }

        // Set the title
        $this->title(l('users'));
        $this->title(l('xs_profile', $user->name));

        $memberships = $user->projectMemberships();
        $memberships = array_filter($memberships, function ($membership) {
            return $this->user->permission($membership->project_id, 'view');
        });

        $assignedTickets = [];
        foreach ($user->assigned_tickets->exec()->fetch_all() as $ticket) {
            if (!$this->user->permission($ticket->project_id, 'view')) {
                continue;
            }

            $assignedTickets[$ticket->project_id][] = $ticket;
        }

        return $this->render('profile/view.phtml', [
            'profile' => $user,
            'memberships' => $memberships,
            'assignedTickets' => $assignedTickets
        ]);
    }
}
