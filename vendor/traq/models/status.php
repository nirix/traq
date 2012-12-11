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
 * Status model.
 *
 * @package Traq
 * @subpackage Models
 * @since 3.0
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Status extends Model
{
    protected static $_name = 'statuses';
    protected static $_properties = array(
        'id',
        'name',
        'status',
        'changelog'
    );

    protected static $_escape = array(
        'name'
    );

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public static function select_options()
    {
        $options = array(l('open') => array(), l('closed') => array());
        foreach (static::fetch_all() as $status) {
            $options[$status->status ? l('open') : l('closed')][] = array('label' => $status->name, 'value' => $status->id);
        }
        return $options;
    }

    // Checks if the model data is valid
    public function is_valid()
    {
        $errors = array();

        // Make sure the name is set.
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        $this->errors = $errors;
        return !count($errors) > 0;
    }
}
