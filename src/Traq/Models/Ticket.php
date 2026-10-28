<?php
/*!
 * Traq
 * Copyright (C) 2009-2026 Jack Polgar
 * Copyright (C) 2012-2026 Traq.io
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
use Traq\ViewModels\RelatedTicketsView;

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
        'tasks',
        'extra',
        'time_proposed',
        'time_worked',
        'created_at',
        'updated_at'
    );

    protected static $_has_many = array(
        'attachments',

        'custom_fields'        => array('model' => 'CustomFieldValue'),
        'history'              => array('model' => 'TicketHistory'),
        'ticket_relationships' => array('model' => 'TicketRelationship')
    );

    protected static $_belongs_to = array(
        'user',
        'project',
        'milestone',
        'component',
        'priority',
        'severity',
        'type',
        'status',

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

    protected $_relatedTickets    = array();

    protected $_custom_fields = [];

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
     * Saves the ticket, keeping the open/closed state in sync with its status.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->_is_new() || in_array('status_id', $this->_changed_properties)) {
            $status = Status::find($this->_data['status_id']);
            $this->_data['is_closed'] = $status && $status->isClosed() ? 1 : 0;
        }

        return parent::save();
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

        if (!is_array($data['extra'])) {
            $data['extra'] = json_decode($data['extra'], true);
        }

        // Set vote count and remove the IDs of
        // users who have voted.
        if (isset($data['extra']['voted'])) {
            $data['votes'] = count($data['extra']['voted']);
            unset($data['extra']['voted']);
        } else {
            $data['votes'] = 0;
        }

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
            $data[$name] = $this->{$name} ? $this->{$name}->__toArray($fields) : null;
            unset($data[$name . '_id']);
        }

        return $data;
    }

    /**
     * Returns the value of the specified custom field ID.
     *
     * @param integer $field_id
     *
     * @return mixed
     */
    public function custom_field_value($field_id)
    {
        $this->fetch_custom_fields();

        return isset($this->_custom_fields[$field_id]) ? $this->_custom_fields[$field_id] : false;
    }

    /**
     * Fetches the tickets custom field values.
     */
    public function fetch_custom_fields()
    {
        if (count($this->_custom_fields)) {
            return $this->_custom_fields;
        }

        $values = CustomFieldValue::select()->where('ticket_id', $this->id)->exec()->fetch_all();
        foreach ($values as $value) {
            $this->_custom_fields[$value->custom_field_id] = $value;
        }

        return $this->_custom_fields;
    }

    /**
     * Toggles the completed status of a task.
     *
     * @param integer $task_id
     */
    public function toggle_task($task_id)
    {
        $this->_data['tasks'][$task_id]['completed'] = $this->_data['tasks'][$task_id]['completed'] ? false : true;
        $this->_set_changed('tasks');
    }

    /**
     * Returns an array of tickets related to this ticket.
     *
     * @return array
     */
    public function relatedTickets(bool $refetch = false): array
    {
        static $tickets;

        if ($tickets && !$refetch) {
            return $tickets;
        }

        $tickets = [];

        $prefix = $this->db()->prefix;
        $query = "SELECT
                tr.id,
                types.id AS relation_type_id,
                types.name AS relation_type_name,
                rt.ticket_id AS ticket_id,
                rt.summary AS summary,
                rt.priority_id AS priority_id,
                rt.project_id AS project_id,
                rp.slug AS project_slug
            FROM {$prefix}ticket_relationships AS tr
            JOIN {$prefix}ticket_relation_types AS types ON tr.relation_type_id = types.id
            JOIN {$prefix}tickets AS rt ON tr.related_ticket_id = rt.id
            JOIN {$prefix}projects AS rp ON rt.project_id = rp.id
            WHERE
                tr.ticket_id = :ticketId";

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':ticketId', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, RelatedTicketsView::class);

        $relations = $stmt->fetchAll();
        foreach ($relations as $relation) {
            $tickets[] = $relation;
        }

        return $tickets;
    }

    /**
     * Processes the data when reading from the database.
     *
     * @access private
     */
    protected function process_data_read()
    {
        $this->_data['extra'] = json_decode(isset($this->_data['extra']) ? $this->_data['extra'] : '', true);

        // Set the voted array
        if (!isset($this->extra['voted']) or !is_array($this->extra['voted'])) {
            $this->_data['extra']['voted'] = array();
        }

        // Tasks
        if (!isset($this->_data['tasks'])) {
            $this->_data['tasks'] = array();
        }

        // Decode tasks
        if (!is_array($this->tasks)) {
            $this->_data['tasks'] = json_decode($this->_data['tasks'], true);
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

        // Encode ticket tasks
        if (is_array($this->_data['tasks'])) {
            $this->tasks = json_encode($this->_data['tasks']);
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

    public static function findByProjectIdAndTicketId(int $projectId, int $ticketId): self|false
    {
        $db = static::db();

        $ticket = static::db()->prepare("SELECT * FROM {$db->prefix}tickets WHERE project_id = :projectId AND ticket_id = :ticketId")->execute([
            'projectId' => $projectId,
            'ticketId' => $ticketId
        ])->fetch();

        if ($ticket) {
            return new static($ticket, false);
        }

        return false;
    }

    public static function getByTicketIdsForProject(int $projectId, array $ticketIds): array
    {
        if (count($ticketIds) === 0) {
            return [];
        }

        $db = static::db();

        // build inIds with named placeholders
        $placeholders = [];
        $ids = [];
        foreach ($ticketIds as $index => $id) {
            $placeholders[] = ":ticketId_{$index}";
            $ids[":ticketId_{$index}"] = $id;
        }
        $inIds = implode(', ', $placeholders);

        $query = "SELECT * FROM {$db->prefix}tickets WHERE project_id = :projectId AND ticket_id IN ($inIds)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':projectId', $projectId, \PDO::PARAM_INT);

        foreach ($ids as $param => $value) {
            $stmt->bindValue($param, $value, \PDO::PARAM_INT);
        }
        $stmt->exec();

        return $stmt->fetchAll();
    }
}
