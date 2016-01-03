<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
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
 * @package Traq\Models
 * @author Jack P.
 * @since 3.0.0
 */
class Milestone extends Model
{
    /**
     * @var string
     */
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
