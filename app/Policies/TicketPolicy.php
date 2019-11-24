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

namespace Traq\Policies;

use Traq\Permissions;
use Traq\User;
use Traq\Ticket;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the ticket.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Ticket  $ticket
     * @return mixed
     */
    public function view(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can create tickets.
     *
     * @param  \Traq\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->group->hasOneOfPermissions([Permissions::PERMISSION_ADMIN, Permissions::PERMISSION_TICKET_CREATE])) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the ticket.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Ticket  $ticket
     * @return mixed
     */
    public function update(User $user, Ticket $ticket)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_TICKET_UPDATE,
        ]);
    }

    public function comment(User $user, Ticket $ticket) {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_TICKET_COMMENT,
        ]);
    }

    public function commentOrUpdate(User $user, Ticket $ticket)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_TICKET_COMMENT,
            Permissions::PERMISSION_TICKET_UPDATE,
        ]);
    }

    /**
     * Determine whether the user can delete the ticket.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Ticket  $ticket
     * @return mixed
     */
    public function delete(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can restore the ticket.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Ticket  $ticket
     * @return mixed
     */
    public function restore(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the ticket.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Ticket  $ticket
     * @return mixed
     */
    public function forceDelete(User $user, Ticket $ticket)
    {
        //
    }
}
