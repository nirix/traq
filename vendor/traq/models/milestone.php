<?php
/*!
 * Traq
 * Copyright (C) 2009-2022 Jack P.
 * Copyright (C) 2012-2022 Traq.io
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

namespace traq\models;

use avalon\core\Kernel as Avalon;
use avalon\database\Model;
use avalon\helpers\Time;

/**
 * Milestone model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Milestone extends Model
{
    public const ACTIVE = 1;
    public const COMPLETE = 2;

    protected static $_name = 'milestones';
    protected static $_properties = array(
        'id',
        'name',
        'slug',
        'codename',
        'info',
        'changelog',
        'due',
        'completed_on',
        'status',
        'is_locked',
        'project_id',
        'displayorder'
    );

    protected static $_escape = array(
        'name'
    );

    // Relations
    protected static $_has_many = array('tickets');
    protected static $_belongs_to = array('project');

    // Filters
    protected static $_filters_before = array(
        'create' => array('_before_create'),
        'save' => array('_before_save')
    );
    protected static $_filters_after = array('construct' => array('_after_construct'));

    /**
     * Easily get the URI to the milestone.
     *
     * @return string
     */
    public function href($uri = null)
    {
        return '/' . $this->project->slug . "/milestone/" . $this->slug . ($uri !== null ? '/' . trim($uri, '/') : '');
    }

    /**
     * Returns an array in the format used for the
     * Form::select() helper.
     */
    public function select_option()
    {
        return array(array('label' => $this->name, 'value' => $this->id));
    }

    /**
     * Returns the number of tickets for the specified status.
     *
     * @param string $status The status of the ticket:
     *     open, closed, total, open_percent, closed_percent
     *
     * @return integer
     */
    public function ticketCount(string $status = 'total')
    {
        // Holder for the counts array.
        static $counts = [];

        if (isset($counts[$this->id])) {
            return $counts[$this->id][$status];
        }

        $query = static::db()->prepare('
            SELECT
                COUNT(t.id) as `total`,
                SUM(
                    CASE s.status
                        WHEN 1 THEN 1
                        ELSE 0
                    END
                ) AS `open`,
                SUM(
                    CASE s.status
                        WHEN 2 THEN 1
                        ELSE 0
                    END
                ) AS `started`,
                SUM(
                    CASE s.status
                        WHEN 0 THEN 1
                        ELSE 0
                    END
                ) AS `closed`
            FROM t3_tickets t
            LEFT JOIN t3_statuses s ON t.status_id = s.id
            WHERE milestone_id = :milestoneId
        ');

        $query->exec(['milestoneId' => $this->id]);
        $results = $query->fetch();

        if (count($results)) {
            $counts[$this->id] = $results;
            $counts[$this->id]['open_percent'] = (int) get_percent($counts[$this->id]['open'] ?? 0, $counts[$this->id]['total']);
            $counts[$this->id]['started_percent'] = (int) get_percent($counts[$this->id]['started'] ?? 0, $counts[$this->id]['total']);
            $counts[$this->id]['closed_percent'] = (int) get_percent($counts[$this->id]['closed'] ?? 0, $counts[$this->id]['total']);
        }
        // dd($status, $counts[$this->id]);
        // Return the requested count index.
        return $counts[$this->id][$status] ?? 0;
    }

    public function ticket_count(string $status = 'total'): int
    {
        return $this->ticketCount($status);
    }

    /**
     * Returns an array of milestone statuses
     * formatted for the Form::select() helper.
     */
    public static function status_select_options()
    {
        return array(
            array('label' => l('active'), 'value' => 1),
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

        // Check if the name is empty
        if (empty($this->_data['name'])) {
            $errors['name'] = l('errors.name_blank');
        }

        // Check if the slug is empty
        if (empty($this->_data['slug'])) {
            $errors['slug'] = l('errors.slug_blank');
        }

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
        if (isset($this->_data['status']) && $this->_data['status'] != 1 && $this->completed_on == null) {
            $this->set('completed_on', "NOW()");
        }

        if (parent::save()) {
            // Check if the status has been changed, if it has, is it completed or cancelled?
            if (
                isset($this->originalStatus, $this->_data['status']) &&
                $this->originalStatus !== (int) $this->_data['status'] &&
                (int) $this->_data['status'] !== static::ACTIVE
            ) {
                $timeline = new Timeline(array(
                    'project_id' => $this->project_id,
                    'owner_id' => $this->id,
                    'action' => (int) $this->_data['status'] === static::COMPLETE ? 'milestone_completed' : 'milestone_cancelled',
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
    protected function _create_slug()
    {
        $this->slug = create_slug($this->slug);
    }

    /**
     * Does required things before creating the row.
     */
    protected function _before_create()
    {
        $this->_create_slug();
    }

    /**
     * Does required things before saving the data.
     */
    protected function _before_save()
    {
        $this->_create_slug();
    }

    /**
     * Clones the status for use in the save method.
     */
    protected function _after_construct()
    {
        // Status
        if (isset($this->_data['status'])) {
            $this->originalStatus = (int) $this->_data['status'];
        }

        // Completed on date from GMT to local
        if (!$this->_is_new() and $this->_data['completed_on'] != null) {
            $this->_data['completed_on'] = Time::gmt_to_local($this->_data['completed_on']);
        }
    }
}
