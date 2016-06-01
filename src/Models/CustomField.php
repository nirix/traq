<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

use Avalon\Language;

/**
 * User group model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class CustomField extends Model
{
    protected static $_validations = [
        'name' => ['required'],
        'slug' => ['required'],
        'type' => ['required']
    ];

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
        return static::where('project_id = ?')->setParameter(0, $project_id)->fetchAll();
    }

    /**
     * Get a list of available custom field types.
     *
     * @return array
     */
    public static function types()
    {
        return [
            'text',
            'select',
            'integer'
        ];
    }

    /**
     * Get an array of custom field types for use with `Form::select`.
     *
     * @return array[]
     */
    public static function typesSelectOptions()
    {
        $options = [];

        foreach (static::types() as $type) {
            $options[] = ['label' => Language::translate($type), 'value' => $type];
        }

        return $options;
    }
}
