<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
 * Copyright (C) 2012-2015 Traq.io
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

use Avalon\Database\Model;

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
        'permissions' => "json_array"
    ];

     /**
      * Returns the permissions for the group and project.
      *
      * @param integer $project_id Project ID
      * @param integer $type_id    Group or Role ID
      * @param string  $type       Permission type (group or role)
      *
      * @return array
      */
    public static function getPermissions($project_id, $type_id, $type = 'usergroup')
    {
        $permissions = [];

        $query = static::select('permissions');
        $query->where($query->expr()->in('project_id', [0, $project_id]))
            ->andWhere('type = :type')
            ->andWhere($query->expr()->in('type_id', [0, $type_id]))
            ->setParameter('type', $type)
            ->orderBy('project_id, type_id', 'ASC');

        foreach ($query->fetchAll() as $row) {
            $permissions = array_merge($permissions, $row->permissions);
        }

        return $permissions;
    }
}
