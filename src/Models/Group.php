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
 * User group model.
 *
 * @author Jack P.
 */
class Group extends Model
{
    protected static $_tableName = 'usergroups';

    // Validations
    protected static $_validates = array(
        'name' => array('required', 'unique')
    );

    // Relations
    protected static $_hasMany = array(
        'users' => array('foreignKey' => 'group_id')
    );

    // Data types
    protected static $_dataTypes = [
        'is_admin' => "boolean"
    ];

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->is_admin == '1' ? true : false;
    }

    /**
     * Returns an array of groups to be used
     * with the Form::select() method.
     *
     * @return array
     */
    public static function selectOptions()
    {
        $options = array();
        foreach (static::all() as $group) {
            $options[] = array('value' => $group->id, 'label' => $group->name);
        }
        return $options;
    }

    /**
     * Returns an array of all group IDs
     *
     * @return array
     */
    public static function allGroupIds()
    {
        $ids = array();

        foreach (static::all() as $group) {
            $ids[] = $group->id;
        }

        return $ids;
    }
}
