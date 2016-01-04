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
    protected static $_tableAlias = 't';

    protected static $_before = [
        'save' => ['updateIsClosed']
    ];

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

    public $isClosing;
    public $isReopening;

    public function __construct(array $data = [], $isNew = true)
    {
        parent::__construct($data, $isNew);

        if (!$isNew) {
            $this->original_status = $this->status_id;
        }
    }

    public function updateIsClosed()
    {
        $this->isClosing = false;
        $this->isReopening = false;

        if ($this->original_status != $this->status_id) {
            $status = Status::find($this->status_id);

            if ($status->status >= 1) {
                if ($this->is_closed) {
                    $this->isClosing = false;
                    $this->isReopening = true;
                }

                $this->is_closed = false;
                $this->isClosing = false;
            } elseif ($status->status == 0) {
                if (!$this->is_closed) {
                    $this->isClosing = true;
                }

                $this->is_closed = true;
            }
        }
    }
}
