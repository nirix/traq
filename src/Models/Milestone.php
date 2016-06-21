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

use DateTime;
use Avalon\Language;

/**
 * Milestone model.
 *
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Milestone extends Model
{
    protected static $_tableAlias = 'm';

    /**
     * @var array
     */
    protected static $_validations = [
        'name' => ['required'],
        'slug' => ['required']
    ];

    /**
     * @var array
     */
    protected static $_dataTypes = [
        'is_locked' => 'boolean',
        'completed_at' => 'datetime'
    ];

    /**
     * @var array
     */
    protected static $_after = [
        'construct' => ['afterConstruct']
    ];

    /**
     * Original status.
     *
     * @var integer
     */
    protected $originalStatus;

    /**
     * Is the milestone being set as complete?
     *
     * @var boolean
     */
    public $isBeingCompleted = false;

    /**
     * @return array[]
     */
    public static function statusSelectOptions()
    {
        return [
            ['label' => Language::translate('active'), 'value' => 1],
            ['label' => Language::translate('completed'), 'value' => 2],
            ['label' => Language::translate('cancelled'), 'value' => 0]
        ];
    }

    /**
     * Custom save method.
     */
    public function save()
    {
        // Set completed date
        if ($this['status'] != 1 and $this['completed_at'] == null) {
            $this['is_locked'] = true;
            $this['completed_at'] = new DateTime;
            $this->isBeingCompleted = true;
        } elseif ($this['status'] == 1) {
            $this['is_locked'] = false;
            $this['completed_at'] = null;
        }

        if (parent::save()) {
            return true;
        }

        return false;
    }

    protected function afterConstruct()
    {
        // Status
        if (isset($this['status'])) {
            $this->originalStatus = $this['status'];
        }
    }
}
