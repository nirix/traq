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
 * Type model.
 *
 * @package Traq
 * @subpackage Models
 * @since 3.0
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Type extends Model
{
    protected static $_name = 'types';
    protected static $_properties = array(
        'id',
        'name',
        'bullet',
        'changelog',
        'template'
    );

    protected static $_escape = array(
        'name',
        'bullet',
        'changelog',
        'template'
    );

    /**
     * Checks if the data is valid.
     *
     * @return bool
     */
    public function is_valid()
    {
        $errors = array();

        // Check if the name is set
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        // Check if the bullet is set
        if ($this->_data['changelog'] and empty($this->_data['bullet'])) {
            $errors['bullet'] = l('errors.type.bullet_blank');
        }

        $this->errors = $errors;
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

        // Get all rows and make a Form::select friendly array
        foreach (static::fetch_all() as $type) {
            $options[] = array('label' => $type->name, 'value' => $type->id);
        }

        return $options;
    }
}
