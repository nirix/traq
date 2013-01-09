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
     * Returns the custom fields for the specified project.
     *
     * @param integer $project_id
     *
     * @return array
     */
    public static function for_project($project_id)
    {
        return static::select()->where('project_id', $project_id)->exec()->fetch_all();
    }

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
     * Returns the fields values formatted for the
     * Form::select() helper.
     *
     * @return array
     */
    public function values_select_options()
    {
        $options = array();

        foreach (explode("\n", $this->values) as $option) {
            $options[] = array('label' => $option, 'value' => $option);
        }

        return $options;
    }

    /**
     * Validates the custom field.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function validate($value)
    {
        switch($this->type) {
            case 'text':
                if ($this->validate_min_length($value)
                and $this->validate_max_length($value)
                and $this->validate_regex($value)) {
                    return true;
                }
                break;

            case 'integer':
                if ($this->validate_min_length($value)
                and $this->validate_max_length($value)
                and $this->validate_regex($value)
                and is_numeric($value)) {
                    return true;
                }
                break;

            case 'select':
                return in_array($value, explode("\n", $this->values));
                break;
        }

        return false;
    }

    /**
     * Validates the minimum length.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    private function validate_min_length($value)
    {
        if ($this->min_length != '') {
            if (strlen($value) < $this->min_length) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the maximum length.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    private function validate_max_length($value)
    {
        if ($this->max_length != '') {
            if (strlen($value) > $this->max_length) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the regex.
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function validate_regex($value)
    {
        if (preg_match("#{$this->regex}#", $value)) {
            return true;
        }

        return false;
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

            // Remove stupid crap
            $this->_data['values'] = str_replace("\r", '', $this->_data['values']);

            return parent::save();
        }

        return false;
    }
}
