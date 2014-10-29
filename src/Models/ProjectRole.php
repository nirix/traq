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

/**
 * Users<>Roles model.
 *
 * @author Jack P.
 */
class ProjectRole extends Model
{
    protected static $_validates = [
        'name' => ['required', 'unique']
    ];

    protected static $_belongsTo = ['project'];

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public static function selectOptions()
    {
        $options = [];
        foreach (static::all() as $role) {
            $options[] = ['label' => $role->name, 'value' => $role->id];
        }
        return $options;
    }
}
