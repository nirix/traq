<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
 * https://github.com/nirix
 * http://traq.io
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
use Radium\Language;

/**
 * User group model.
 *
 * @author Jack P.
 */
class CustomField extends Model
{
    protected static $_dataTypes = [
        'ticket_type_ids' => 'json_array'
    ];

    /**
     * Returns the custom fields for the specified project.
     *
     * @param integer $project_id
     *
     * @return array
     */
    public static function forProject($project_id)
    {
        return static::where('project_id = ?', $project_id)->fetchAll();
    }

    /**
     * Returns an array of IDs belonging to custom fields.
     *
     * @param object $project
     *
     * @return array
     */
    public static function getIds(Project $project = null)
    {
        $ids = [];

        // Get fields for the project if one was passed, otherwise get all.
        $fields = $project ? static::forProject($project->id) : static::all();

        foreach ($fields as $field) {
            $ids[] = $field->id;
        }

        return $ids;
    }

    /**
     * Returns an array of slugs belonging to custom fields.
     *
     * @param object $project
     *
     * @return array
     */
    public static function getSlugs(Project $project = null)
    {
        $slugs = [];

        // Get fields for the project if one was passed, otherwise get all.
        $fields = $project ? static::forProject($project->id) : static::all();

        foreach ($fields as $field) {
            $slugs[] = $field->slug;
        }

        return $slugs;
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
    public static function typesSelectOptions()
    {
        $options = [];

        foreach (static::types() as $type) {
            $options[] = ['label' => Language::translate($type), 'value' => $type];
        }

        return $options;
    }

    /**
     * Returns the fields values formatted for the
     * Form::select() helper.
     *
     * @return array
     */
    public function valuesSelectOptions()
    {
        $options = [];

        foreach (explode("\n", $this->values) as $option) {
            $options[] = ['label' => $option, 'value' => $option];
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
                // Multiple select
                if ($this->multiple) {
                    foreach ($value as $v) {
                        if (!in_array($v, explode("\n", $this->values))) {
                            return false;
                        }
                    }
                    return true;
                }
                // Single select
                else {
                    return in_array($value, explode("\n", $this->values));
                }
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
        if ($this->min_length != "0") {
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
        if ($this->max_length != "0") {
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

        // Check if the slug is empty
        if (empty($this->_data['slug'])) {
            $errors['slug'] = l('errors.slug_blank');
        }

        // Make sure the slug isnt in use
        $slug = static::select('id')->where('id', ($this->_is_new() ? 0 : $this->id), '!=')->where('slug', $this->_data['slug'])->where('project_id', $this->_data['project_id']);
        if ($slug->exec()->row_count()) {
            $errors['slug'] = l('errors.slug_in_use');
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
     * Returns a string of CSS classes to be used in the custom field
     * `div` wrapper in forms to easily show or hide for relevant
     * ticket types.
     *
     * @return string
     */
    public function typeCssClasses()
    {
        $classes = [];

        foreach ($this->ticket_type_ids as $type_id) {
            $classes[] = "field-for-type-{$type_id}";
        }

        return implode(" ", $classes);
    }

    /**
     * Saves the model data.
     *
     * @return boolean
     */
    public function save()
    {
        if ($this->validates()) {
            // Remove stupid crap
            $this->values = str_replace("\r", '', $this->values);

            return parent::save();
        }

        return false;
    }
}
