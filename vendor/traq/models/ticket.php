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
 * Ticket model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Ticket extends Model
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

    protected static $_escape = array(
        'summary'
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
     * Returns the URI for the ticket.
     *
     * @return string
     */
    public function href($uri = null)
    {
        return "/{$this->project->slug}/tickets/{$this->ticket_id}" . ($uri !== null ? '/' . trim($uri, '/') : '');
    }

    /**
     * Adds a vote to the ticket.
     *
     * @param object $user
     *
     * @return bool
     */
    public function add_vote($user_id)
    {
        // Make sure the voted array exists
        if (!is_array($this->_data['extra']['voted'])) {
            $this->_data['extra']['voted'] = array();
        }

        // Make sure they havent voted before
        if (!in_array($user_id, $this->_data['extra']['voted'])) {
            $this->votes++;
            $this->_data['extra']['voted'][] = $user_id;
            $this->_set_changed('extra');
            return true;
        } else {
            return false;
        }
    }

    /**
     * Custom save method for the ticket
     * so we can do what we need with the timeline and such.
     *
     * @return bool
     */
    public function save()
    {
        // Is this a new ticket?
        if ($this->_is_new()) {
            // Get the next ticket id and update
            // the value for the next ticket.
            $this->ticket_id = $this->project->next_tid;
            $this->project->next_tid++;
            $this->project->save();
        }

        // Update ticket open/closed state if ticket status has changed.
        $status = Status::find($this->_data['status_id']);
        if (isset($this->_data['is_closed'])) {
            if (($this->_data['is_closed'] == 1 and $status->status == 1)
            or ($this->_data['is_closed'] == 0 and $status->status == 0)) {
                $this->is_closed = ($status->status == 1 ? 0 : 1);
            }
        }

        if (parent::save()) {
            // New ticket?
            if ($this->_is_new()) {
                // Timeline entry
                $timeline = new Timeline(array(
                    'project_id' => $this->project_id,
                    'owner_id' => $this->id,
                    'action' => 'ticket_created',
                    'data' => $this->status_id || 1,
                    'user_id' => $this->user_id
                ));

                $timeline->save();

                // Created notification
                Notification::send_for_ticket('created', $this);

                // Assigned to notification
                if ($this->_data['assigned_to_id'] != 0) {
                    Notification::send_to($this->_data['assigned_to_id'], 'ticket_assigned', array('ticket' => $this, 'project' => $this->project));
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Saves without doing anything with the timeline, notifications, etc.
     *
     * @return boolean
     */
    public function quick_save()
    {
        return parent::save();
    }

    /**
     * Used to update the ticket properties.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update_data($data)
    {
        $user = Avalon::app()->user;

        $to_values = array();
        $changes = array();
        $save_queue = array();

        // Loop over the data
        foreach ($data as $field => $value) {
            // Check if the value is different
            if (isset($this->_data[$field]) and $this->_data[$field] == $value) {
                continue;
            }

            // If this field is an attachment, check permissions
            if ($field == 'attachment' and !$user->permission($this->project_id, 'add_attachments')) {
                continue;
            }

            // Get the to and from values for different fields
            $from = $to = null;
            switch($field) {
                case 'assigned_to_id':
                    $from = $this->assigned_to_id == 0 ? null : $this->assigned_to->id;
                    $to = $value;
                    break;

                case 'status_id':
                case 'type_id':
                    $accessor = str_replace('_id', '', $field);
                    $class = '\\traq\\models\\' . ucfirst($accessor);
                    $to_values[$field] = $class::find($value);

                    $from = $this->$accessor->name;
                    $to = $to_values[$field]->name;
                    break;

                case 'summary':
                    $from = $this->summary;
                    $to = $value;
                    break;

                case 'attachment':
                    $to = $value;
                    break;

                case 'milestone_id':
                case 'version_id':
                    $to_values[$field] = Milestone::find($value);

                    if ($this->milestone) {
                        $from = $this->milestone->name;
                    }

                    if ($to_values[$field]) {
                        $to = $to_values[$field]->name;
                    }
                    break;

                default:
                    $accessor = str_replace('_id', '', $field);
                    $class = '\\traq\models\\' . ucfirst($accessor);
                    $to_values[$field] = $class::find($value);

                    if ($this->$accessor) {
                        $from = $this->$accessor->name;
                    }

                    if ($to_values[$field]) {
                        $to = $to_values[$field]->name;
                    }
                    break;
            }

            // One last value check...
            if ($from == $to) {
                continue;
            }

            // Change data
            $change = array(
                'property' => str_replace('_id', '', $field),
                'from' => $from,
                'to' => $to
            );

            // Has the status changed?
            if ($field == 'status_id' and $this->status_id != $value) {
                if ($this->status->status != $to_values[$field]->status) {
                    $this->is_closed = $to_values[$field]->status ? 0 : 1;
                    $change['action'] = $to_values[$field]->status == 1 ? 'reopen' : 'close';

                    $save_queue[] = new Timeline(array(
                        'project_id' => $this->project_id,
                        'owner_id' => $this->id,
                        'action' => $change['action'] == 'close' ? 'ticket_closed' : 'ticket_reopened',
                        'data' => $to_values[$field]->id,
                        'user_id' => $user->id
                    ));

                    $this->_is_closing = $change['action'] == 'close' ? true : false;
                    $this->_is_reopening= $change['action'] == 'reopen' ? true : false;
                }
            }
            // Attaching a file?
            elseif ($field == 'attachment' and isset($_FILES['attachment']) and isset($_FILES['attachment']['name'])) {
                $save_queue[] = new Attachment(array(
                    'name' => $_FILES['attachment']['name'],
                    'contents' => base64_encode(file_get_contents($_FILES['attachment']['tmp_name'])),
                    'type' => $_FILES['attachment']['type'],
                    'size' => $_FILES['attachment']['size'],
                    'user_id' => $user->id,
                    'ticket_id' => $this->id
                ));
                $change['action'] = 'add_attachment';
            }

            // Set value
            if (in_array($field, static::$_properties)) {
                $this->set($field, $value);
            }

            $changes[] = $change;
        }

        // Any changes, or perhaps a comment?
        if (count($changes) > 0 or !empty(Request::$post['comment'])) {
            $save_queue[] = new TicketHistory(array(
                'user_id' => $user->id,
                'ticket_id' => $this->id,
                'changes' => count($changes) > 0 ? json_encode($changes) : '',
                'comment' => isset(Request::$post['comment']) ? Request::$post['comment'] : ''
            ));

            if (!empty(Request::$post['comment'])) {
                $save_queue[] = new Timeline(array(
                    'project_id' => $this->project_id,
                    'owner_id' => $this->id,
                    'action' => 'ticket_comment',
                    'user_id' => $user->id
                ));
            }
        }

        // Save
        if ($this->save()) {
            // Loop over the save queue and save
            // each object
            foreach ($save_queue as $model) {
                $model->save();
            }

            // Closed notification
            if (isset($this->_is_closing) and $this->_is_closing) {
                Notification::send_for_ticket('closed', $this);
            }
            // Reopened notification
            elseif (isset($this->_is_reopening) and $this->_is_reopening) {
                Notification::send_for_ticket('reopened', $this);
            }
            // Updated notification
            else {
                Notification::send_for_ticket('updated', $this);
            }

            // Assigned to notification
            if (in_array('assigned_to_id', $this->_changed_properties) and $this->_data['assigned_to_id'] != 0) {
                Notification::send_to($this->_data['assigned_to_id'], 'ticket_assigned', array('ticket' => $this, 'project' => $this->project));
            }

            return true;
        }
        // Error saving
        else {
            return false;
        }
    }

    /**
     * Returns the table columns allowed on the listing page.
     *
     * @return array
     */
    public static function listing_columns()
    {
        return array(
            'ticket_id',
            'summary',
            'status',
            'owner',
            'type',
            'component',
            'milestone',
            'created_at',
            'updated_at',
            'votes'
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

        // Check the summary
        if (empty($this->_data['summary'])) {
            $errors['summary'] = l('errors.tickets.summary_blank');
        }

        // Check the body
        if (empty($this->_data['body'])) {
            $errors['body'] = l('errors.tickets.description_blank');
        }

        $this->errors = $errors;
        return !count($errors) > 0;
    }

    /**
     * Returns the ticket data as an array.
     *
     * @param array $fields Fields to return
     *
     * @return array
     */
    public function __toArray($fields = null)
    {
        $data = parent::__toArray($fields);
        $data['id'] = $data['ticket_id'];
        unset($data['ticket_id']);

        // Extra data to fetch
        $relations = array(
            'project'     => array('id', 'name'),
            'user'        => array('id', 'username', 'name'),
            'assigned_to' => array('id', 'username', 'name'),
            'milestone'   => array('id', 'name'),
            'version'     => array('id', 'name'),
            'component'   => array('id', 'name'),
            'status'      => array('id', 'name'),
            'priority'    => array('id', 'name'),
            'severity'    => array('id', 'name'),
            'type'        => array('id', 'name'),
        );

        // Loop over the relations
        foreach ($relations as $name => $fields) {
            // Add the relation data and remove its ID
            // from the main array
            $data[$name] = $this->$name ? $this->$name->__toArray($fields) : null;
            unset($data[$name . '_id']);
        }

        return $data;
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

    /**
     * Delete ticket and all data
     */
    public function delete()
    {
        if (parent::delete()) {
            // Delete attachments
            foreach ($this->attachments->exec()->fetch_all() as $attachment) {
                $attachment->delete();
            }

            // Delete history
            foreach ($this->history->exec()->fetch_all() as $update) {
                $update->delete();
            }

            // Delete timeline data
            $timeline = Timeline::select()->where('action', 'ticket%', 'LIKE')->where('owner_id', $this->id)->exec();
            foreach ($timeline->fetch_all() as $row) {
                $row->delete();
            }

            // Subscriptions
            $subscriptions = Subscription::select()->where('type', 'ticket')->where('object_id', $this->id)->exec();
            foreach ($subscriptions->fetch_all() as $row) {
                $row->delete();
            }

            return true;
        }

        return false;
    }
}
