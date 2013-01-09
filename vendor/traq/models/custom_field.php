<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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
class CustomField extends Model
{
    protected static $_name = 'custom_fields';
    protected static $_properties = array(
        'id',
        'name',
        'type',
        'values',
        'multiple',
        'default_value',
        'regex',
        'min_length',
        'max_length',
        'is_required',
        'project_id'
    );

    /**
     * Returns the models properties.
     *
     * @return array
     */
    public static function properties()
    {
        return static::$_properties;
    }

    /**
     * Returns an array containing valid field types.
     *
     * @return array
     */
    public static function types()
    {
        return array(
            'text',
            'select',
            'integer'
        );
    }

    /**
     * Returns an array of valid field types formatted
     * for the Form::select() helper.
     *
     * @return array
     */
    public static function types_select_options()
    {
        $options = array();

        foreach (static::types() as $type) {
            $options[] = array('label' => l($type), 'value' => $type);
        }

        return $options;
    }

    /**
     * Checks if the model data is valid.
     *
     * @return boolean
     */
    public function is_valid()
    {
        $errors = array();

        // Make sure the name is set
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        // Make sure the type is set
        if (empty($this->_data['type'])) {
            $errors['type'] = l('errors.type_blank');
        }

        // Text and integer field
        if ($this->type == 'text' or $this->type == 'integer') {
            // Make sure regex is set
            if (empty($this->_data['regex'])) {
                $errors['regex'] = l('errors.regex_blank');
            }
        }
        // Select field
        elseif ($this->type == 'select') {
            // Make sure there are some values is set
            if (empty($this->_data['values'])) {
                $errors['values'] = l('errors.values_blank');
            }
        }

        // Set errors and return
        $this->errors = $errors;
        return !count($errors) > 0;
    }

    /**
     * Saves the model data.
     *
     * @return boolean
     */
    public function save()
    {
        if ($this->is_valid()) {
            // Defaults
            $defaults = array(
                'values'        => "NULL",
                'multiple'      => 0,
                'default_value' => "NULL",
                'regex'         => "NULL",
                'min_length'    => "NULL",
                'max_length'    => "NULL",
                'is_required'   => 0,
            );

            // Merge defaults with currently set data
            $this->_data = array_merge($defaults, $this->_data);

            return parent::save();
        }

        return false;
    }
}
