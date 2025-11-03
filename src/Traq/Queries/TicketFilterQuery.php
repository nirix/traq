<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

namespace Traq\Queries;

use Avalon\Database;
use Traq\ViewModels\TicketView;

// Legacy
use avalon\core\Kernel as Avalon;
use traq\models\CustomField;
use traq\models\Status;
use traq\models\User;

/**
 * Ticket filter query.
 *
 * @package Traq
 * @subpackage Queries
 * @since 3.9.0
 */
class TicketFilterQuery
{
    public function __construct(
        protected int $projectId,
        protected string $sortField = 'created_at',
        protected string $sortDirection = 'DESC',
    ) {}

    public function query(bool $withLimit = false): string
    {
        $prefix = Database::connection()->prefix;

        $limit = $withLimit ? 'LIMIT :limit OFFSET :offset' : '';

        return "
            SELECT
                t.ticket_id,
                t.summary,
                t.user_id,
                t.assigned_to_id,
                t.votes,
                t.created_at,
                t.updated_at,
                t.is_closed,
                ru.name AS owner,
                au.name AS assignee,
                tp.name AS type,
                m.name AS milestone,
                m.slug AS milestone_slug,
                v.name AS version,
                v.slug AS version_slug,
                c.name AS component,
                s.name AS `status`,
                p.name AS priority,
                sv.name AS severity

            FROM {$prefix}tickets t
            LEFT JOIN {$prefix}users ru ON t.user_id = ru.id
            LEFT JOIN {$prefix}users au ON t.assigned_to_id = au.id
            LEFT JOIN {$prefix}milestones m ON t.milestone_id = m.id
            LEFT JOIN {$prefix}milestones v ON t.version_id = v.id
            LEFT JOIN {$prefix}statuses s ON t.status_id = s.id
            LEFT JOIN {$prefix}components c ON t.component_id = c.id
            LEFT JOIN {$prefix}types tp ON t.type_id = tp.id
            LEFT JOIN {$prefix}priorities p ON t.priority_id = p.id
            LEFT JOIN {$prefix}severities sv ON t.severity_id = sv.id

            WHERE
                t.project_id = :projectId

            ORDER BY {$this->sortField} {$this->sortDirection}

            {$limit}
        ";
    }

    public function getRowCount(): int
    {
        $db = Database::connection();
        $query = $this->query();

        $stmt = $db->prepare($query);
        $stmt->bindValue(':projectId', $this->projectId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getTickets(?int $limit = null, ?int $offset = null): array
    {
        $db = Database::connection();
        $query = $this->query($limit > 0);

        $stmt = $db->prepare($query);
        $stmt->bindValue(':projectId', $this->projectId, \PDO::PARAM_INT);
        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        }
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, TicketView::class);

        return $stmt->fetchAll();
    }

    //--------------------------------------------------------------
    // Legacy code
    //--------------------------------------------------------------

    private $sql = array();
    private $custom_field_sql = array();
    private $filters = array();
    private $project;

    /**
     * Processes a filter.
     *
     * @param string $filed
     * @param array $values
     */
    public function process($field, $values)
    {
        if ($field == 'search') {
            $values = is_array($values) ? $values : array($values);
        } elseif (!is_array($values)) {
            $values = explode(',', $values);
        }

        $values = array_map(fn($val) => addslashes($val), $values);

        $condition = '';
        if (substr($values[0], 0, 1) == '!') {
            $condition = 'NOT';
            $values[0] = substr($values[0], 1);
        }

        // Add to filters array
        $this->filters[$field] = array('prefix' => ($condition == 'NOT' ? '!' : ''), 'values' => array());

        if ($values[0] == '' or end($values) == '') {
            $this->filters[$field]['values'][] = '';
        }

        if (count($values)) {
            $this->add($field, $condition, $values);
        }
    }

    /**
     * Checks the values and constructs the query.
     *
     * @param string $field
     * @param string $condition
     * @param array $values
     */
    private function add($field, $condition, $values)
    {
        $query_values = array();

        if (!count($values)) {
            return;
        }

        // Milestone, version, status, type and component
        if (in_array($field, array('milestone', 'status', 'type', 'version', 'component', 'priority', 'severity'))) {
            $class = "\\traq\\models\\" . ucfirst($field == 'version' ? 'milestone' : $field);
            foreach ($values as $value) {
                // What column to use when
                // looking up row.
                switch ($field) {
                    // Milestone and version
                    case 'milestone':
                    case 'version':
                        $find = 'slug';
                        break;

                    // Everything else
                    default:
                        $find = 'name';
                        break;
                }

                // Status, type, priority and severity
                if (in_array($field, array('status', 'type', 'priority', 'severity'))) {
                    // Find row and add ID to query values if it exists
                    if ($field == 'status' and ($value == 'allopen' or $value == 'allclosed')) {
                        foreach (Status::select('id')->where('status', ($value == 'allopen' ? 1 : 0))->exec()->fetch_all() as $status) {
                            $query_values[] = $status->id;
                        }
                    } elseif ($row = $class::find($find, $value) and $row) {
                        $query_values[] = $row->id;
                    }
                }
                // Everything else
                else {
                    if ($row = $class::select()->where('project_id', Avalon::app()->project->id)->where($find, $value)->exec()->fetch()) {
                        $query_values[] = $row->id;
                    }
                }
            }

            // Sort values low to high
            asort($query_values);

            // Value
            $value = "IN (" . implode(',', $query_values) . ")";

            // Add to query if there's any values
            if (count($query_values)) {
                $this->sql[] = "`{$field}_id` {$condition} {$value}";
            }

            $this->filters[$field]['values'] = array_merge($query_values, $this->filters[$field]['values']);
        }
        // Summary and description
        elseif (in_array($field, array('summary', 'description'))) {
            $class = "\\traq\\models\\" . ucfirst($field);
            $query_values = array();
            foreach ($values as $value) {
                if (!empty($value)) {
                    $field_name = ($field == 'summary' ? 'summary' : 'body');
                    $query_values[] = "`{$field_name}` {$condition} LIKE '%" . str_replace('*', '%', $value) . "%'";
                }
            }

            if (count($query_values)) {
                $this->sql[] = "(" . implode(' OR ', $query_values) . ")";
                $this->filters[$field]['values'] = $values;
            }
        }
        // Owner and Assigned to
        elseif (in_array($field, array('owner', 'assigned_to'))) {
            $column = ($field == 'owner') ? 'user' : $field;

            $query_values[] = 0;
            foreach ($values as $value) {
                if (!empty($value)) {
                    if ($user = User::find('username', $value)) {
                        $query_values[] = $user->id;
                    }
                }
            }

            // Sort values low to high
            asort($query_values);

            // Value
            $value = "IN (" . implode(',', $query_values) . ")";

            // Add to query if there's any values
            if (count($query_values)) {
                $this->sql[] = "`{$column}_id` {$condition} {$value}";
                $this->filters[$field]['values'] = array_merge($values, $this->filters[$field]['values']);
            }
        }
        // Search
        elseif ($field == 'search') {
            $value = str_replace('*', '%', implode('%', $values));

            $value = \addslashes($value);

            $this->sql[] = "(`summary` LIKE '%{$value}%' OR `body` LIKE '%{$value}%')";
            $this->filters['search']['values'] = $values;
        }
        // Custom fields
        elseif (in_array($field, array_keys(custom_field_filters_for($this->project)))) {
            $custom_field = CustomField::find('slug', $field);
            $this->filters[$field]['label'] = $custom_field->name;
            $this->filters[$field]['values'] = $values;

            // Sort values low to high
            asort($values);
            $values = array_map(
                fn($val) => '"' . $val . '"',
                $values
            );

            if (count($values) >= 1 && !empty($values[0])) {
                $this->custom_field_sql[] = "
                    `fields`.`custom_field_id` = {$custom_field->id}
                    AND `fields`.`value` {$condition} IN ('" . implode("','", $values) . "')
                    AND `fields`.`ticket_id` = `" . Database::connection()->prefix . "tickets`.`id`
                ";
            }
        }
    }

    /**
     * Returns filters.
     *
     * @return array
     */
    public function filters()
    {
        return $this->filters;
    }

    /**
     * Returns the query.
     *
     * @return string
     */
    public function sql()
    {
        $sql = array();

        if (count($this->custom_field_sql)) {
            foreach ($this->custom_field_sql as $index => $customFieldSql) {
                $sql[] = "JOIN `" . Database::connection()->prefix . "custom_field_values` AS `fields{$index}` ON (" . str_replace('`fields`', "`fields{$index}`", $customFieldSql) . ")";
            }
        }

        $sql[] = " WHERE `project_id` = {$this->project->id}";

        if (count($this->sql)) {
            $sql[] = "AND " . implode(" AND ", $this->sql);
        }

        return implode(" ", $sql);
    }
}
