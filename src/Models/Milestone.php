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
use Radium\Kernel as Radium;
use Radium\Helpers\Time;

/**
 * Milestone model.
 *
 * @author Jack P.
 */
class Milestone extends Model
{
    // Validations
    protected static $_validates = array(
        'name' => array('required'),
        'slug' => array('required')
    );

    // Before filters
    protected static $_before = array(
        'create' => array('_beforeCreate'),
        'save'   => array('_beforeSave')
    );

    // After filters
    protected static $_after = array(
        'construct' => array('_afterConstruct')
    );

    /**
     * Project relation.
     *
     * @return Traq\Models\Project
     */
    public function project()
    {
        return $this->belongsTo('Project');
    }

    /**
     * Tickets relation.
     *
     * @return array
     */
    public function tickets()
    {
        return $this->hasMany('Ticket');
    }

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
        return array(array('label' => $this->name, 'value' => $this->id));
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
        return $this->tickets()->innerJoin('tickets', 'statuses', 'statuses', "statuses.id = tickets.status_id")
            ->where("`statuses`.`status` = 2")
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
     * Returns an array of milestone statuses
     * formatted for the Form::select() helper.
     */
    public static function statusSelectOptions()
    {
        return array(
            array('label' => l('active'),    'value' => 1),
            array('label' => l('completed'), 'value' => 2),
            array('label' => l('cancelled'), 'value' => 0),
        );
    }

    /**
     * Checks if the models data is valid.
     *
     * @return bool
     */
    public function is_valid()
    {
        $errors = array();

        // Check if the slug is in use
        $milestone_slug = Milestone::select('slug')->where('id', $this->_is_new() ? 0 : $this->_data['id'], '!=')
            ->where('slug', $this->_data['slug'])->where('project_id', $this->project_id);

        if ($milestone_slug->exec()->row_count()) {
            $errors['slug'] = l('errors.slug_in_use');
        }

        $this->errors = $errors;
        return !count($errors) > 0;
    }

    /**
     * Custom save method.
     */
    public function save()
    {
        // Set completed date
        if ($this->_data['status'] != 1 and $this->completed_on == null) {
            $this->set('completed_on', "NOW()");
        }

        if (parent::save()) {
            // Check if the status has been changed, if it has, is it completed or cancelled?
            if ($this->original_status != $this->_data['status'] and $this->_data['status'] != 1) {
                $timeline = new Timeline(array(
                    'project_id' => $this->project_id,
                    'owner_id' => $this->id,
                    'action' => $this->_data['status'] == 2 ? 'milestone_completed' : 'milestone_cancelled',
                    'user_id' => Avalon::app()->user->id
                ));
                $timeline->save();
            }

            return true;
        }

        return false;
    }

    /**
     * Converts the slug to be URI safe.
     */
    protected function _createSlug()
    {
        $this->slug = URI::createSlug($this->slug);
    }

    /**
     * Does required things before creating the row.
     */
    protected function _beforeCreate()
    {
        $this->_create_slug();
    }

    /**
     * Does required things before saving the data.
     */
    protected function _beforeSave()
    {
        $this->_create_slug();
    }

    /**
     * Clones the status for use in the save method.
     */
    protected function _afterConstruct()
    {
        // Status
        if (isset($this->status)) {
            $this->original_status = $this->status;
        }

        // Completed on date from GMT to local
        if (!$this->_isNew and $this->completed_on != null) {
            $this->completed_on = Time::gmtToLocal($this->completed_on);
        }
    }
}
