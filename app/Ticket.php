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
 * @property int $id
 * @property int $ticket_id
 * @property int $project_id
 * @property int $user_id
 * @property int $milestone_id
 * @property int $version_id
 * @property int $status_id
 * @property int $type_id
 * @property int $priority_id
 * @property string $summary
 * @property string $description
 */
class Ticket extends Model
{
    public const ACTION_CREATE = 'ticket.created';
    public const ACTION_UPDATED = 'ticket.updated';
    public const ACTION_CLOSED = 'ticket.closed';
    public const ACTION_REOPENED = 'ticket.reopened';

    protected $fillable = [
        'summary',
        'description',
        'milestone_id',
        'type_id',
        'priority_id',
        'version_id',
        'status_id',
    ];

    protected $casts = [
        'milestone_id' => 'int',
        'type_id' => 'int',
        'priority_id' => 'int',
        'version_id' => 'int',
        'status_id' => 'int',
        'is_closed' => 'boolean',
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function version()
    {
        return $this->belongsTo(Milestone::class, 'version_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updates()
    {
        return $this->hasMany(TicketUpdate::class);
    }

    public function isClosed()
    {
        return $this->is_closed === true;
    }
}
