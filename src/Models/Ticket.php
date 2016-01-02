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

use Avalon\Database\Model;

/**
 * Ticket model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 2.0.0
 */
class Ticket extends Model
{
    protected static $_validations = [
        'ticket_id'    => ['required'],
        'summary'      => ['required'],
        'info'         => ['required'],
        'user_id'      => ['required'],
        'project_id'   => ['required'],
        'milestone_id' => ['required']
    ];

    protected static $_dataTypes = [
        'tasks' => 'json_array',
        'extra' => 'json_array'
    ];
}
