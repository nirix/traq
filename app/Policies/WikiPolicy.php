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
use Illuminate\Auth\Access\HandlesAuthorization;
use Traq\WikiPage;

class WikiPolicy
{
    use HandlesAuthorization;

    public function view(User $user): bool
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_WIKI_VIEW,
        ]);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Traq\User  $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_WIKI_CREATE,
        ]);
    }

    /**
     * Determine whether the user can update models.
     *
     * @param User $user
     * @param WikiPage $model
     *
     * @return bool
     */
    public function update(User $user, WikiPage $model): bool
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_WIKI_UPDATE,
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param WikiPage $model
     *
     * @return bool
     */
    public function delete(User $user, WikiPage $model): bool
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_USER_DELETE,
        ]);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param WikiPage $model
     *
     * @return bool
     */
    public function restore(User $user, WikiPage $model): bool
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_USER_DELETE,
        ]);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param WikiPage $model
     *
     * @return bool
     */
    public function forceDelete(User $user, WikiPage $model): bool
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_USER_DELETE,
        ]);
    }
}
