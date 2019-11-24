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
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $name
 * @property string $codename
 * @property string $slug
 * @property string $description
 * @property int $status
 * @property Carbon $due_at
 * @property Carbon $completed_at
 * @property int $display_order
 * @property int $project_id
 */
class Milestone extends Model
{
    public const STATUS_ACTIVE = 0;
    public const STATUS_COMPLETED = 1;

    protected $fillable = [
        'name',
        'codename',
        'slug',
        'description',
        'status',
        'due_at',
        'completed_at',
        'display_order',
        'project_id',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'due_at' => 'date',
        'status' => 'int',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function isClosed(): bool
    {
        return $this->status === static::STATUS_COMPLETED;
    }

    public function getStatusCounts()
    {
        static $counts = [];

        if (isset($counts[$this->id])) {
            return $counts[$this->id];
        }

        $query = DB::table('tickets')
            ->select(DB::raw('count(*) as tickets, statuses.id as status_id, statuses.status as status'))
            ->where('project_id', $this->project_id)
            ->where('milestone_id', $this->id)
            ->join('statuses', 'tickets.status_id', '=', 'statuses.id')
            ->groupBy(
                'tickets.status_id',
                'statuses.id',
                'statuses.status'
            );

        $results = $query->get();

        $counts[$this->id] = [];

        foreach ($results as $result) {
            $counts[$this->id][$result->status_id] = [
                'status_id' => $result->status_id,
                'tickets' => $result->tickets,
                'status' => $result->status,
            ];
        }

        return $counts[$this->id];
    }

    public function getClosedCount(): int
    {
        $counts = $this->getStatusCounts();

        $count = \array_reduce(
            $counts,
            function ($carry, $countSet) {
                if ($countSet['status'] === Status::STATUS_CLOSED) {
                    $carry += $countSet['tickets'];
                }

                return $carry;
            }
        );

        return $count ?? 0;
    }

    public function getStartedCount(): int
    {
        $counts = $this->getStatusCounts();

        $count = \array_reduce(
            $counts,
            function ($carry, $countSet) {
                if ($countSet['status'] === Status::STATUS_STARTED) {
                    $carry += $countSet['tickets'];
                }

                return $carry;
            }
        );

        return $count ?? 0;
    }

    public function getTotalCount(): int
    {
        $counts = $this->getStatusCounts();

        $count = \array_reduce(
            $counts,
            function ($carry, $countSet) {
                $carry += $countSet['tickets'];

                return $carry;
            }
        );

        return $count ?? 0;
    }

    public function getClosedPercent(): int
    {
        $completed = $this->getClosedCount();
        $total = $this->getTotalCount();

        return $completed > 0 ? ($completed * 100 / $total) : 0;
    }

    public function getStartedPercent(): int
    {
        $started = $this->getStartedCount();
        $total = $this->getTotalCount();

        return $started > 0 ? ($started * 100 / $total) : 0;
    }

    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', static::STATUS_COMPLETED);
    }
    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
