<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
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

use DateTime;
use Avalon\Database\Model;
use Avalon\Helpers\Time;
use Avalon\Language;

/**
 * Milestone model.
 *
 * @author Jack P.
 */
class Milestone extends Model
{
    /**
     * @var array
     */
    protected static $_belongsTo = [
        'project'
    ];

    /**
     * @var array
     */
    protected static $_hasMany = [
        'tickets'
    ];

    /**
     * @var array
     */
    protected static $_validates = [
        'name' => ['required'],
        'slug' => ['required']
    ];

    /**
     * @var array
     */
    protected static $_after = [
        'construct' => ['afterConstruct']
    ];

    /**
     * @var array
     */
    protected static $_dataTypes = [
        'is_locked'    => "boolean",
        'completed_at' => "datetime"
    ];

    /**
     * Is the Milestone being marked as completed?
     *
     * @var boolean
     */
    public $isBeingCompleted = false;

    /**
     * Easily get the URI to the milestone.
     *
     * @return string
     */
    public function href($uri = null)
    {
        return "/{$this->project()->slug}/milestone/{$this->slug}" . ($uri !== null ? '/' . trim($uri, '/') : '');
    }

    /**
     * Returns an array in the format used for the
     * Form::select() helper.
     */
    public function selectOption()
    {
        return [['label' => $this->name, 'value' => $this->id]];
    }

    public function ticketPercent($status = 'closed')
    {
        $total = $this->tickets()->rowCount();
        $count = $this->ticketCount($status);

        if ($total > 0 and $count > 0) {
            return round($count / $total * 100);
        }

        return 0;
    }

    /**
     * Returns the count of open tickets.
     *
     * @return integer
     */
    public function openTicketCount()
    {
        return $this->ticketCount('open');
    }

    /**
     * Returns the count of closed tickets.
     *
     * @return integer
     */
    public function closedTicketCount()
    {
        return $this->ticketCount('closed');
    }

    /**
     * Returns the count of started tickets.
     *
     * @return integer
     */
    public function startedTicketCount()
    {
        return $this->tickets()->join('tickets', 'statuses', 'statuses', "statuses.id = tickets.status_id")
            ->where("statuses.status = ?", 2)
            ->rowCount();
    }

    /**
     * Returns the number of tickets for the specified status.
     *
     * @param string $status
     *
     * @return integer
     */
    public function ticketCount($status = 'closed')
    {
        return $count = $this->tickets()
            ->where('is_closed = ?', ($status == 'open' ? 0 : 1))
            ->rowCount();
    }

    /**
     * Returns an array of milestone statuses formatted for the Form::select() helper.
     *
     * @return array
     */
    public static function statusSelectOptions()
    {
        return [
            ['label' => Language::translate('active'),    'value' => 1],
            ['label' => Language::translate('completed'), 'value' => 2],
            ['label' => Language::translate('cancelled'), 'value' => 0],
        ];
    }

    /**
     * Custom save method.
     */
    public function save()
    {
        // Set completed date
        if ($this->status != 1 and $this->completed_at == null) {
            $this->is_locked        = true;
            $this->completed_at     = new DateTime;
            $this->isBeingCompleted = true;
        } elseif ($this->status == 1) {
            $this->is_locked    = false;
            $this->completed_at = null;
        }

        if (parent::save()) {
            return true;
        }

        return false;
    }

    /**
     * Clones the status for use in the save method.
     */
    protected function afterConstruct()
    {
        // Status
        if (isset($this->status)) {
            $this->original_status = $this->status;
        }
    }
}
