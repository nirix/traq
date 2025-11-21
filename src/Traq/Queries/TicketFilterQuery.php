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
use Traq\Models\CustomField;

/**
 * Ticket filter query.
 *
 * @package Traq
 * @subpackage Queries
 * @since 3.9.0
 */
class TicketFilterQuery
{
    protected static array $fieldMapping = [
        'summary' => 't.summary',
        'description' => 't.body',
        'owner' => 'ru.name',
        'assigned_to' => 'au.name',
        'milestone' => 'm.slug',
        'status' => 's.name',
        'status_type' => 's.status',
        'type' => 'tp.name',
        'version' => 'v.slug',
        'component' => 'c.name',
        'priority' => 'p.name',
        'severity' => 'sv.name',
        'votes' => 't.votes',
        'created_at' => 't.created_at',
        'updated_at' => 't.updated_at',
    ];

    protected array $filters = [];
    protected array $customFields = [];
    protected array $customFieldSlugs = [];

    public function __construct(
        protected int $projectId,
        protected string $sortField = 'created_at',
        protected string $sortDirection = 'DESC',
        string $queryString = ''
    ) {
        $this->processQueryString($queryString);
        $this->customFields = CustomField::forProject($this->projectId);

        foreach ($this->customFields as $index => $field) {
            $this->customFieldSlugs[] = $field->slug;
        }
    }

    public function query(bool $withLimit = false): string
    {
        $prefix = Database::connection()->prefix;

        $limit = $withLimit ? 'LIMIT :limit OFFSET :offset' : '';

        $filtersSql = $this->buildFiltersSql();

        $customFieldSql =  $this->buildCustomFieldSql();
        $customFieldSelect = $customFieldSql['select'] ? ',' . $customFieldSql['select'] : '';

        $sortField = static::$fieldMapping[$this->sortField] ?? 't.ticket_id';

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
                t.priority_id,
                sv.name AS severity,
                t.severity_id,
                t.project_id,
                pr.slug AS project_slug
                {$customFieldSelect}

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
            LEFT JOIN {$prefix}projects pr ON t.project_id = pr.id
            {$customFieldSql['join']}

            WHERE
                t.project_id = :projectId

            {$filtersSql}

            ORDER BY {$sortField} {$this->sortDirection}

            {$limit}
        ";
    }

    public function getRowCount(): int
    {
        $db = Database::connection();
        $query = $this->query();

        $stmt = $db->prepare($query);
        $stmt->bindValue(':projectId', $this->projectId, \PDO::PARAM_INT);

        foreach ($this->getValueParams() as $param => $value) {
            if (is_int($value)) {
                $stmt->bindValue($param, (int) $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($param, $value, \PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function getTickets(?int $limit = null, ?int $offset = null): array
    {
        $db = Database::connection();
        $query = $this->query($limit !== null && $offset !== null);

        $stmt = $db->prepare($query);
        $stmt->bindValue(':projectId', $this->projectId, \PDO::PARAM_INT);

        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        }

        foreach ($this->getValueParams() as $param => $value) {
            if (is_int($value)) {
                $stmt->bindValue($param, $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($param, $value, \PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        // Fetch as TicketView objects
        $stmt->setFetchMode(\PDO::FETCH_CLASS, TicketView::class);

        $tickets = array_map(function (TicketView $ticket) {
            // Populate custom fields
            foreach ($this->customFields as $index => $field) {
                $prop = "custom_field_{$index}";
                $value = $ticket->{$prop};
                if ($value) {
                    $value = json_decode($ticket->{$prop});
                }

                if ($value && $field->type === 'integer') {
                    $value = (int) $value;
                }

                $ticket->{$field->slug} = $value;
                unset($ticket->{$prop});
            }

            return $ticket;
        }, $stmt->fetchAll());

        return $tickets;
    }

    public function processQueryString(string $queryString): void
    {
        parse_str($queryString, $params);

        // page number and ordering aren't filters
        unset($params['page']);
        unset($params['order_by']);

        foreach ($params as $field => $value) {
            // Get condition from first value and update if necessary
            $condition = strlen($value) >= 1 && $value[0] === '!' ? 'NOT' : '';
            if ($condition === 'NOT') {
                $value = substr($value, 1);
            }

            $values = explode(',', $value);

            if ($field === 'status' && (in_array('allclosed', $values) || in_array('allopen', $values) || in_array('allstarted', $values))) {
                $field = 'status_type';
                $originalValues = $values;
                $values = [];

                if (in_array('allclosed', $originalValues)) {
                    $values[] = 0;
                }
                if (in_array('allopen', $originalValues)) {
                    $values[] = 1;
                }
                if (in_array('allstarted', $originalValues)) {
                    $values[] = 2;
                }
            }

            $filter = [
                'condition' => $condition,
                'values' => $values,
            ];

            $this->filters[$field] = $filter;
        }
    }

    protected function buildFiltersSql(): string
    {
        $sqlParts = [];
        $fieldMapping = static::$fieldMapping;
        $customFields = [];

        foreach ($this->customFields as $index => $field) {
            $customFields[$field->slug] = $field;
            $fieldMapping[$field->slug] = "cf_{$index}.value";
        }

        foreach ($this->filters as $field => $filter) {
            $condition = $filter['condition'];
            $values = $filter['values'];

            $fieldName = isset($fieldMapping[$field])
                ? $fieldMapping[$field]
                : ($field === 'q' ? 'q' : null);

            if ($fieldName === null) {
                continue;
            }

            // Build SQL for each filter type
            if ($fieldName === 'q') {
                $likeClauses = [];
                foreach ($values as $index => $value) {
                    $likeClauses[] = "t.summary {$condition} LIKE :{$field}_{$index}";
                    $likeClauses[] = "t.ticket_id {$condition} LIKE :{$field}_{$index}";
                }

                $sqlParts[] = '(' . implode(' OR ', $likeClauses) . ')';
            } elseif (in_array($field, ['milestone', 'status', 'type', 'version', 'component', 'priority', 'severity', 'assigned_to', 'status_type'])) {
                // Named placeholders for each value
                $placeholders = [];
                foreach ($values as $index => $value) {
                    $placeholders[] = ":{$field}_{$index}";
                }
                $placeholders = implode(',', $placeholders);

                $sqlParts[] = "{$fieldName} {$condition} IN ({$placeholders})";
            } elseif (in_array($field, ['summary', 'description', 'owner'])) {
                $likeClauses = [];
                foreach ($values as $index => $value) {
                    $likeClauses[] = "{$fieldName} {$condition} LIKE :{$field}_{$index}";
                }
                $sqlParts[] = '(' . implode(' OR ', $likeClauses) . ')';
            } elseif (in_array($field, array_keys($customFields))) {
                $likeClauses = [];
                foreach ($values as $index => $value) {
                    $likeClauses[] = "{$fieldName} {$condition} LIKE :{$field}_{$index}";
                }
                $sqlParts[] = '(' . implode(' OR ', $likeClauses) . ')';
            }
        }

        $sql = count($sqlParts) ? 'AND ' . implode(' AND ', $sqlParts) : '';

        return $sql;
    }

    protected function getValueParams(): array
    {
        $params = [];

        foreach ($this->filters as $field => $filter) {
            foreach ($filter['values'] as $index => $value) {
                if (in_array($field, ['q', 'summary', 'description', ...$this->customFieldSlugs])) {
                    $value = '%' . str_replace('*', '%', $value) . '%';
                }

                $params[":{$field}_{$index}"] = $value;
            }
        }

        return $params;
    }

    protected function buildCustomFieldSql(): array
    {
        $prefix = Database::connection()->prefix;

        $selectSql = [];
        $joinSql = [];

        foreach ($this->customFields as $index => $field) {
            $selectSql[] = "cf_{$index}.value AS custom_field_{$index}";
            $joinSql[] = "LEFT JOIN {$prefix}custom_field_values cf_{$index} ON t.id = cf_{$index}.ticket_id AND cf_{$index}.custom_field_id = {$field->id}";
        }

        return [
            'select' => count($selectSql) ? implode(',', $selectSql) : null,
            'join' => count($joinSql) ? implode("\n", $joinSql) : null,
        ];
    }
}
