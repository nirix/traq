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
use Traq\WikiPage;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $next_ticket_id
 */
class Project extends Model
{
    protected $fillable = [
        'name',
        'codename',
        'slug',
        'description',
        'default_status_id',
        'default_priority_id',
        'enable_wiki',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'enable_wiki' => 'boolean',
    ];

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function activeMilestones()
    {
        return $this->milestones()
            ->where('status', '=', Milestone::STATUS_ACTIVE)
            ->orderBy('display_order', 'ASC');
    }

    public function completedMilestones()
    {
        return $this->milestones()
            ->where('status', '=', Milestone::STATUS_COMPLETED)
            ->orderBy('display_order', 'ASC');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function defaultStatus()
    {
        return $this->belongsTo(Status::class, 'default_status_id');
    }

    public function defaultPriority()
    {
        return $this->belongsTo(Priority::class, 'default_priority_id');
    }

    public function incrementTicketId()
    {
        $this->next_ticket_id += 1;
    }

    public function timelineEvents()
    {
        return $this->hasMany(TimelineEvent::class);
    }

    public function wikiPages()
    {
        return $this->hasMany(WikiPage::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
