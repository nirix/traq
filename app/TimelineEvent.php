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

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $project_id
 * @property integer $user_id
 * @property string $owner_type
 * @property integer $owner_id
 * @property array $data
 * @property string $action
 * @property User $user
 * @property Carbon $created_at
 */
class TimelineEvent extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'owner_type',
        'owner_id',
        'data',
        'action',
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReadableSummary()
    {
        $translationString = \sprintf(
            'timeline.%s',
            \str_replace('.', '_', $this->action)
        );

        if ($this->objectTypeIs(Ticket::class)) {
            $ticket = $this->getObject();

            return __(
                $translationString,
                [
                    'ticket_id' => $ticket->ticket_id,
                    'ticket_summary' => $ticket->summary,
                ] + $this->getData()
            );
        }
    }

    public function getData()
    {
        return \is_array($this->data) ? $this->data : [];
    }

    public function objectTypeIs(string $class)
    {
        return $this->owner_type === $class;
    }

    public function getObject()
    {
        $class = $this->owner_type;

        return $class::find($this->owner_id);
    }
}
