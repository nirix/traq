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

namespace Traq;

final class Permissions
{
    public const PERMISSION_ADMIN = 'ADMIN';

    // Users
    public const PERMISSION_USER_CREATE = 'CREATE_USER';
    public const PERMISSION_USER_UPDATE = 'UPDATE_USER';
    public const PERMISSION_USER_DELETE = 'DELETE_USER';

    // User Groups
    public const PERMISSION_USER_GROUP_CREATE = 'CREATE_USER_GROUP';
    public const PERMISSION_USER_GROUP_UPDATE = 'UPDATE_USER_GROUP';
    public const PERMISSION_USER_GROUP_DELETE = 'DELETE_USER_GROUP';

    // Projects
    public const PERMISSION_PROJECT_VIEW = 'VIEW_PROJECT';
    public const PERMISSION_PROJECT_CREATE = 'CREATE_PROJECT';
    public const PERMISSION_PROJECT_UPDATE = 'UPDATE_PROJECT';
    public const PERMISSION_PROJECT_DELETE = 'DELETE_PROJECT';

    // Milestones
    public const PERMISSION_MILESTONE_CREATE = 'CREATE_MILESTONE';
    public const PERMISSION_MILESTONE_UPDATE = 'UPDATE_MILESTONE';
    public const PERMISSION_MILESTONE_DELETE = 'DELETE_MILESTONE';

    // Ticket permissions
    public const PERMISSION_TICKET_CREATE = 'CREATE_TICKET';
    public const PERMISSION_TICKET_UPDATE = 'UPDATE_TICKET';
    public const PERMISSION_TICKET_DELETE = 'DELETE_TICKET';
    public const PERMISSION_TICKET_COMMENT = 'COMMENT_ON_TICKET';

    // Wiki permissions
    public const PERMISSION_WIKI_VIEW = 'VIEW_WIKI';
    public const PERMISSION_WIKI_CREATE = 'CREATE_WIKI';
    public const PERMISSION_WIKI_UPDATE = 'UPDATE_WIKI';
    public const PERMISSION_WIKI_DELETE = 'DELETE_WIKI';

    private static $permissions;

    /**
     * Get the users permisions.
     *
     * Get the users group and project role permissions.
     *
     * @param User $user
     * @param Project $project
     *
     * @return array
     */
    public static function getPermissions(
        User $user,
        Project $project = null
    ): array {
        return static::$permissions ?? static::$permissions = $user->group->permissions;
    }

    /**
     * Check if the user has the requested permission.
     *
     * @param User $user
     * @param string $permission
     * @param Project $project
     *
     * @return boolean
     */
    public static function userHasPermission(
        User $user,
        string $permission,
        Project $project = null
    ): bool {
        $userPermissions = static::getPermissions($user, $project);

        return \in_array($permission, $userPermissions, true);
    }

    /**
     * Check if the user has one of the requested permissions.
     *
     * @param User $user
     * @param array $permissions
     * @param Project $project
     *
     * @return boolean
     */
    public static function userHasOneOfPermissions(
        User $user,
        array $permissions,
        Project $project = null
    ): bool {
        $userPermissions = static::getPermissions($user, $project);

        foreach ($permissions as $permission) {
            if (\in_array($permission, $userPermissions, true)) {
                return true;
            }
        }

        return false;
    }
}
