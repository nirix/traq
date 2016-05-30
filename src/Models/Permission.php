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

namespace Traq\Models;

/**
 * Permission model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Permission extends Model
{
    protected static $_dataTypes = [
        'permissions' => 'json_array'
    ];

    /**
     * Get permissions for the user and project and merge them.
     *
     * @param User    $user
     * @param Project $project
     *
     * @return array
     */
    public static function getPermissions(User $user = null, Project $project = null)
    {
        $query = static::connection()->createQueryBuilder();

        $query->select(
            'd.permissions AS group_defaults',
            'gp.permissions AS group_permissions'
        );

        $query->from(Permission::tableName(), 'd');

        // Group defaults
        $query->leftJoin(
            'd',
            Permission::tableName(),
            'gp',
            "gp.project_id = 0 AND gp.type = 'usergroup' AND gp.type_id = :group_id"
        );

        // Group defaults
        $query->where('d.project_id = 0');
        $query->andWhere("d.type = 'usergroup'");
        $query->andWhere('d.type_id = 0');
        $query->setParameter(':group_id', $user ? $user->group_id : 3);

        // Project?
        if ($project) {
            // Project defaults for usergroup
            $query->addSelect('pgd.permissions AS project_group_defaults');

            $query->leftJoin(
                'd',
                Permission::tableName(),
                'pgd',
                "pgd.project_id = :project_id AND pgd.type = 'usergroup' AND pgd.type_id = 0"
            );

            $query->setParameter(':project_id', $project->id);

            // Project permissions for usergroup
            $query->addSelect('pgp.permissions AS project_group_permissions');

            $query->leftJoin(
                'd',
                Permission::tableName(),
                'pgp',
                "pgp.project_id = :project_id AND pgp.type = 'usergroup' AND pgp.type_id = :group_id"
            );

            // TODO: project roles
        }

        $result = $query->execute();
        $result = $result->fetch();

        $result = [
            'group_defaults' => null,
            'group_permissions' => null,
            'project_group_defaults' => null,
            'project_group_permissions' => null,

            'role_defaults' => null,
            'role_permissions' => null,
            'project_role_defaults' => null,
            'project_role_permissions' => null
        ] + $result;

        // Convert from JSON to an array
        $result['group_defaults'] = json_decode($result['group_defaults'], true) ?: [];
        $result['group_permissions'] = json_decode($result['group_permissions'], true) ?: [];
        $result['project_group_defaults'] = json_decode($result['project_group_defaults'], true) ?: [];
        $result['project_group_permissions'] = json_decode($result['project_group_permissions'], true) ?: [];

        return array_merge(
            $result['group_defaults'],
            $result['group_permissions'],
            $result['project_group_defaults'],
            $result['project_group_permissions']
        );
    }
}
