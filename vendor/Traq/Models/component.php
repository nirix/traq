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
 * Component model.
 *
 * @package Traq
 * @subpackage Models
 * @since 3.0
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Component extends Model
{
    protected static $_name = 'components';
    protected static $_properties = array(
        'id',
        'name',
        'project_id'
    );

    protected static $_escape = array(
        'name'
    );

    /**
     * Checks if the model data is valid.
     *
     * @return bool
     */
    public function is_valid()
    {
        $errors = array();

        // Check if the name is empty
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        $this->errors = $errors;
        return !count($errors) > 0;
    }

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public static function select_options($project_id)
    {
        $options = array();
        foreach (static::select()->where('project_id', $project_id)->order_by('name', 'ASC')->exec()->fetch_all() as $component) {
            $options[] = array('label' => $component->name, 'value' => $component->id);
        }
        return $options;
    }
}
