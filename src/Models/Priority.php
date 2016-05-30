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
 * Ticket priority model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Priority extends Model
{
    protected static $_validations = [
        'name' => ['required']
    ];

    public static function selectOptions($valueField = 'id')
    {
        $options = [];

        foreach (static::select('id', 'name')->orderBy('id', 'ASC')->execute()->fetchAll() as $row) {
            $options[] = ['label' => $row['name'], 'value' => $row[$valueField]];
        }

        return $options;
    }
}
