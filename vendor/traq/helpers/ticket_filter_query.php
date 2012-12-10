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
    private $filters = array();

    /**
     * Processes a filter.
     *
     * @param string $filed
     * @param array $values
     */
    public function process($field, $values)
    {
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
        if (in_array($field, array('milestone', 'status', 'type', 'version', 'component'))) {
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

                // Find row and add ID to query values if it exists
                if ($value == 'allopen' or $value == 'allclosed') {
                    foreach (Status::select('id')->where('status', ($value == 'allopen' ? 1 : 0))->exec()->fetch_all() as $status) {
                        $query_values[] = $status->id;
                    }
                } elseif ($row = $class::find($find, $value) and $row) {
                    $query_values[] = $row->id;
                }
            }

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
                    $query_values[] = "`{$field}` {$condition} LIKE '%" . str_replace('*', '%', $value) . "%'";
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

            foreach ($values as $value) {
                if (!empty($value)) {
                    $query_values[] = User::find('username', $value)->id;
                }
            }

            // Value
            $value = "IN (" . implode(',', $query_values) . ")";

            // Add to query if there's any values
            if (count($query_values)) {
                $this->sql[] = "`{$column}_id` {$condition} {$value}";
                $this->filters[$field]['values'] = array_merge($values, $this->filters[$field]['values']);
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
        if (count($this->sql)) {
            return "WHERE" . implode(" AND ", $this->sql);
        }
    }
}
