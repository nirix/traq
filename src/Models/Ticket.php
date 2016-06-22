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
 * Ticket model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Ticket extends Model
{
    protected static $_tableAlias = 't';

    protected static $_validations = [
        'summary' => ['required'],
        'body' => ['required'],
        'user_id' => ['required'],
        'project_id' => ['required'],
        'milestone_id' => ['required'],
        'type_id' => ['required'],
        'status_id' => ['required'],
        'priority_id' => ['required'],
        'severity_id' => ['required']
    ];

    protected static $_hasMany = [
        'history' => ['model' => 'TicketHistory']
    ];

    protected static $_dataTypes = [
        'is_closed' => 'boolean',
        'is_private' => 'boolean',
        'tasks' => 'json_array',
        'extra' => 'json_array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Whether or not the ticket is being closed.
     *
     * @var boolean
     */
    public $isClosing = false;

    /**
     * Whether or not the ticket is being reopened.
     *
     * @var boolean
     */
    public $isReopening = false;
}
