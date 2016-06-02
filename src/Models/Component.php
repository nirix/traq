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

/**
 * Component model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Component extends Model
{
    protected static $_validations =[
        'name' => ['required']
    ];

    /**
     * @return array[]
     */
    public static function selectOptions($projectId, $valueField = 'id')
    {
        $options = [];
        $rows = static::select('id', 'name')
            ->where('project_id = ?')
            ->setParameter(0, $projectId)
            ->execute()
            ->fetchAll();

        foreach ($rows as $row) {
            $options[] = ['label' => $row['name'], 'value' => $row[$valueField]];
        }

        return $options;
    }
}
