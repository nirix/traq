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
 * Users<>Roles model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class ProjectRole extends Model
{
    protected static $_name = 'project_roles';
    protected static $_properties = array(
        'id',
        'name',
        'assignable',
        'project_id'
    );

    protected static $_belongs_to = array('project');

    // Validates the model data
    public function is_valid()
    {
        $errors = array();

        // Make sure the name is not empty...
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        // Set errors to be accessible
        if (count($errors) > 0) {
            $this->errors = $errors;
        }

        return !count($errors);
    }

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public static function select_options()
    {
        $options = array();
        foreach (static::fetch_all() as $role) {
            $options[] = array('label' => $role->name, 'value' => $role->id);
        }
        return $options;
    }
}
