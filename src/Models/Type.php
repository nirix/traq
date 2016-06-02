<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

/**
 * Ticket type model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Type extends Model
{
    protected static $_validations = [
        'name'   => ['required', 'unique'],
        'bullet' => ['required']
    ];

    /**
     * @return array[]
     */
    public static function selectOptions($valueField = 'id')
    {
        $options = [];
        $types = static::select('id', 'name')->execute()->fetchAll();

        foreach ($types as $type) {
            $options[] = ['label' => $type['name'], 'value' => $type[$valueField]];
        }

        return $options;
    }
}
