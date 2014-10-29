<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

use Traq\Models\Project;
use Traq\Models\Milestone;
use Traq\Models\Ticket;

/**
 * Subscription model.
 *
 * @author Jack P.
 */
class Subscription extends Model
{
    protected static $_belongsTo = array('user');

    private $object;

    /**
     * Fetch subscriptions for specified project.
     *
     * @param integer $project_id
     *
     * @return array
     */
    public static function fetchAllFor($project_id)
    {
        return static::select()->where('project_id = ?', $project_id)->fetchAll();
    }

    /**
     * Returns the subscribed object.
     *
     * @return object
     */
    public function object() {
        if ($this->object !== null) {
            return $this->object;
        }

        switch ($this->type) {
            case 'project':
                $this->object = Project::find($this->object_id);
                break;

            case 'milestone':
                $this->object = Milestone::find($this->object_id);
                break;

            case 'ticket':
                $this->object = Ticket::find($this->object_id);
                break;
        }

        return $this->object;
    }
}
