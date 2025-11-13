<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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
 * Status model.
 *
 * @package Traq
 * @subpackage Models
 * @since 3.0
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Status extends Model
{
    public const STATUS_CLOSED = 0;
    public const STATUS_OPEN = 1;
    public const STATUS_STARTED = 2;

    protected static $_name = 'statuses';
    protected static $_properties = array(
        'id',
        'name',
        'status',
        'changelog'
    );

    protected static $_escape = array(
        'name'
    );

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public static function select_options()
    {
        $options = [
            l('open') => [],
            l('started') => [],
            l('closed') => [],
        ];

        foreach (static::fetch_all() as $status) {
            $key = l($status->isOpen()
                ? 'open'
                : ($status->isStarted() ? 'started' : 'closed'));
            $options[$key][] = ['label' => $status->name, 'value' => $status->id];
        }

        return $options;
    }

    // Checks if the model data is valid
    public function is_valid()
    {
        $errors = array();

        // Make sure the name is set.
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        $this->errors = $errors;
        return !count($errors) > 0;
    }

    public function isOpen(): bool
    {
        return (int) $this->status === static::STATUS_OPEN;
    }

    public function isStarted(): bool
    {
        return (int) $this->status === static::STATUS_STARTED;
    }

    public function isClosed(): bool
    {
        return (int) $this->status === static::STATUS_CLOSED;
    }

    public function __toArray($fields = null)
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'status' => (int) $this->status,
            'changelog' => (bool) $this->changelog,
        ];
    }
}
