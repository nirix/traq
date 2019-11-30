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
use Illuminate\Support\Facades\Lang;

class TicketUpdate extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'change_data',
    ];

    protected $casts = [
        'change_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function changeDataWithInfo(): array
    {
        $changes = [];
        foreach ($this->change_data as $field => $change) {
            $langKey = "tickets.{$field}";

            $change['label'] = Lang::has($langKey) ? Lang::get($langKey) : $field;

            $change['field'] = $field;
            $changes[$field] = $change;
        }

        return $changes;
    }
}
