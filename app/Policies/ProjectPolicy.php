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
use Traq\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Project $project = null)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_PROJECT_CREATE,
            Permissions::PERMISSION_PROJECT_UPDATE,
            Permissions::PERMISSION_PROJECT_DELETE,
        ], $project);
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_PROJECT_VIEW,
        ]);
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  \Traq\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_PROJECT_CREATE,
        ]);
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Project  $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_PROJECT_UPDATE,
        ]);
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Project  $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_PROJECT_DELETE,
        ]);
    }

    /**
     * Determine whether the user can restore the project.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Project  $project
     * @return mixed
     */
    public function restore(User $user, Project $project)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_PROJECT_DELETE,
        ]);
    }

    /**
     * Determine whether the user can permanently delete the project.
     *
     * @param  \Traq\User  $user
     * @param  \Traq\Project  $project
     * @return mixed
     */
    public function forceDelete(User $user, Project $project)
    {
        return $user->hasOneOfPermissions([
            Permissions::PERMISSION_ADMIN,
            Permissions::PERMISSION_PROJECT_DELETE,
        ]);
    }
}
