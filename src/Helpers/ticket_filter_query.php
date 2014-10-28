<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

namespace traq\helpers;

use avalon\core\Kernel as Avalon;
use avalon\Database;
use traq\models\CustomField;
use traq\models\Status;
use traq\models\User;

/**
 * Ticket filter query builder.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Helpers
 */
class TicketFilterQuery
{
    private $sql = array();
    private $custom_field_sql = array();
    private $filters = array();
    private $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

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

        $condition = '';
        if (substr($values[0], 0, 1) == '!') {
            $condition = 'NOT';
            $values[0] = substr($values[0], 1);
        }

        // Add to filters array
        $this->filters[$field] = array('prefix' => ($condition == 'NOT' ? '!' :''), 'values' => array());

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

            if (count($values) == 1 && !empty($values[0])) {
                $this->custom_field_sql[] = "
                    `fields`.`custom_field_id` = {$custom_field->id}
                    AND `fields`.`value` IN ('" . implode("','", $values) . "')
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
            $sql[] = "JOIN `" . Database::connection()->prefix . "custom_field_values` AS `fields` ON (" . implode(' AND ', $this->custom_field_sql) . ")";
        }

        $sql[] = " WHERE `project_id` = {$this->project->id}";

        if (count($this->sql)) {
            $sql[] = "AND " . implode(" AND ", $this->sql);
        }

        return implode(" ", $sql);
    }
}
