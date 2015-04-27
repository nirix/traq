<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
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
 * Users<>Roles model.
 *
 * @author Jack P.
 */
class UserRole extends Model
{
    // Allow easy access to the project and role models
    protected static $_belongsTo = array(
        'project', 'user',

        'role' => array(
            'model'    => 'ProjectRole',
            'localKey' => 'project_role_id'
        )
    );

    /**
     * Returns an array of the project members.
     *
     * @return array
     */
    public static function projectMembers($project_id)
    {
        $members = [];

        // Loop over the relations and add the user to the array
        foreach (static::where('project_id = ?', $project_id)->fetchAll() as $relation) {
            $members[] = $relation->user();
        }

        return $members;
    }

    public function __toArray($fields = null)
    {
        $data = parent::__toArray($fields);
        $data['user'] = $this->user()->__toArray();
        $data['role'] = $this->role()->__toArray();
        return $data;
    }
}
