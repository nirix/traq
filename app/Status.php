<?php
/*!
 * Traq
 *
 * Copyright (C) 2009-2019 Jack P.
 * Copyright (C) 2012-2019 Traq.io
 * https://github.com/nirix
 * https://traq.io
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property int $status
 */
class Status extends Model
{
    public const STATUS_CLOSED = 0;
    public const STATUS_NEW = 1;
    public const STATUS_STARTED = 2;

    protected $casts = [
        'status' => 'int',
    ];

    public function isOpen(): bool
    {
        return $this->status === static::STATUS_NEW || $this->status === static::STATUS_STARTED;
    }

    public function isClosed(): bool
    {
        return $this->status === static::STATUS_CLOSED;
    }
}
