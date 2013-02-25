<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

namespace traq\models;

use avalon\database\Model;

/**
 * User group model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Group extends Model
{
    protected static $_name = 'usergroups';
    protected static $_properties = array(
        'id',
        'name',
        'is_admin',
    );

    protected static $_escape = array(
        'name'
    );

    // Relations
    protected static $_has_many = array('users' => array('foreign_key' => 'group_id'));

    /**
     * Returns an array of groups to be used
     * with the Form::select() method.
     *
     * @return array
     */
    public static function select_options()
    {
        $options = array();
        foreach (static::fetch_all() as $group) {
            $options[] = array('value' => $group->id, 'label' => $group->name);
        }
        return $options;
    }

    /**
     * Returns an array of all group IDs
     *
     * @return array
     */
    public static function all_group_ids()
    {
        $ids = array();

        foreach (static::fetch_all() as $group) {
            $ids[] = $group->id;
        }

        return $ids;
    }

    /**
     * Checks if the groups data is valid.
     *
     * @return bool
     */
    public function is_valid()
    {
        $errors = array();

        // Make sure the name is set...
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        $this->errors = $errors;
        return !count($errors) > 0;
    }
}
