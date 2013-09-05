<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

use avalon\database\Model;
use avalon\core\Kernel as Avalon;
use avalon\http\Request;

use traq\helpers\Notification;

/**
 * Traq 3.0 Ticket model stripped down with a few
 * extra methods for upgrade purposes.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class TicketUpgrade extends Model
{
    protected static $_name = 'tickets';
    protected static $_properties = array(
        'id',
        'ticket_id',
        'summary',
        'body',
        'user_id',
        'project_id',
        'milestone_id',
        'version_id',
        'component_id',
        'type_id',
        'status_id',
        'priority_id',
        'severity_id',
        'assigned_to_id',
        'is_closed',
        'is_private',
        'votes',
        'extra',
        'created_at',
        'updated_at'
    );

    protected static $_has_many = array(
        'attachments',

        'history' => array('model' => 'TicketHistory')
    );

    protected static $_belongs_to = array(
        'user', 'project', 'milestone', 'component',
        'priority', 'severity', 'type', 'status',

        // Relations with different models and such
        'assigned_to' => array('model' => 'User'),
        'version'     => array('model' => 'Milestone'),
    );

    protected static $_filters_after = array(
        'construct' => array('process_data_read')
    );

    protected static $_filters_before = array(
        'create' => array('process_data_write'),
        'save' => array('process_data_write')
    );

    /**
     * Deletes a user from the voted list.
     *
     * @param integer $id Users ID
     */
    public function delete_voter($id)
    {
        foreach ($this->_data['extra']['voted'] as $k => $v) {
            if ($v == $id) {
                unset($this->_data['extra']['voted'][$k]);
            }
        }

        $this->votes = count($this->_data['extra']['voted']);
    }

    /**
     * Removes the custom fields index from the extra field.
     */
    public function remove_custom_fields()
    {
        unset($this->_data['extra']['custom_fields']);
    }

    /**
     * Checks if the models data is valid.
     *
     * @return bool
     */
    public function is_valid()
    {
        $errors = array();

        // Check the summary
        if (empty($this->_data['summary'])) {
            $errors['summary'] = l('errors.tickets.summary_blank');
        }

        // Check the body
        if (empty($this->_data['body'])) {
            $errors['body'] = l('errors.tickets.description_blank');
        }

        // Merge errors
        $this->errors = array_merge($errors, $this->errors);
        return !count($this->errors) > 0;
    }

    /**
     * Processes the data when reading from the database.
     *
     * @access private
     */
    protected function process_data_read()
    {
        $this->extra = json_decode(isset($this->_data['extra']) ? $this->_data['extra'] : '', true);

        // Set the voted array
        if (!isset($this->extra['voted']) or !is_array($this->extra['voted'])) {
            $this->_data['extra']['voted'] = array();
        }

        // Set the custom_fields array
        if (!isset($this->extra['custom_fields']) or !is_array($this->extra['custom_fields'])) {
            $this->_data['extra']['custom_fields'] = array();
        }
    }

    /**
     * Processes the data when saving to the database.
     *
     * @access private
     */
    protected function process_data_write()
    {
        if (isset($this->_data['extra']) and is_array($this->_data['extra'])) {
            $this->extra = json_encode($this->_data['extra']);
        }
    }
}
