<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace Traq\Models;

use Avalon\Database\Model;

/**
 * Permission model.
 *
 * @author Jack P.
 */
class Permission extends Model
{
    /**
     * Returns the permissions for the group and project.
     *
     * @param integer $group_id
     * @param integer $type_id
     * @param string $type
     *
     * @return array
     */
    public static function getPermissions($project_id, $type_id = 0, $type = 'usergroup')
    {
        // Fetch the permission rows and merge them with the defaults
        $rows = static::select()
            ->where('project_id = ?', $project_id)
            ->andWhere('type = ?', $type)
            ->andWhere('type_id = ?', $type_id)
            ->fetchAll();

        $rows = array_merge(static::defaults($project_id, $type_id, $type), $rows);

        // Loop over the permissions and make it
        // easy to access the permission values.
        $permissions = array();
        foreach ($rows as $permission) {
            $permissions[$permission->action] = $permission;
        }

        // And return them...
        return $permissions;
    }

    /**
     * Returns the default permissions.
     *
     * @param integer $project_id
     * @param integer $type_id
     * @param string $type
     *
     * @return array
     */
    public static function defaults($project_id = 0, $type_id = 0, $type = 'usergroup')
    {
        // Fetch the defaults
        $defaults = static::select()
            ->where('type = ?', $type)
            ->andWhere('type_id = ?', $type_id)
            ->andWhere('project_id IN (' . ($project_id > 0 ? "0,{$project_id}" : '0') . ')')
            ->fetchAll();

        // If we're fetching a specific group,
        // also fetch the defaults for all groups.
        if ($type_id > 0) {
            $defaults = array_merge(static::defaults($project_id, 0, $type), $defaults);
        }

        // Loop over the defaults and push them to a new array
        // this will stop duplicates from the overall defaults
        // and the defaults for specific groups.
        $permissions = array();
        foreach ($defaults as $permission) {
            $permissions[$permission->action] = $permission;
        }

        // And return them...
        return $permissions;
    }
}
