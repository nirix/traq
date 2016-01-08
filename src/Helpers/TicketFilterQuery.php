<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

namespace Traq\Helpers;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;

/**
 * Ticket filtering.
 *
 * @package Traq\Helpers
 * @author Jack P.
 * @since 3.0.0
 */
class TicketFilterQuery
{
    /**
     * @var QueryBuilder
     */
    protected $builder;

    /**
     * @var ExpressionBuilder
     */
    protected $expr;

    /**
     * @var array
     */
    public $query;

    /**
     * @var array
     */
    public $filters = [];

    /**
     * @param object $builder ticket query builder
     * @param array  $query   key value array of filters
     */
    public function __construct(QueryBuilder $builder, $query)
    {
        $this->builder = $builder;
        $this->expr    = $builder->expr();
        $this->query   = $query;

        $this->filter();
    }

    /**
     * Filter tickets.
     *
     * @param array $query key value array of filters
     */
    public function filter(array $query = null)
    {
        if (!$query) {
            $query = $this->query;
        } else {
            $this->query = $query;
        }

        if (isset($query['open'])) {
            $this->allOpen();
        } elseif (isset($query['started'])) {
            $this->allStarted();
        } elseif (isset($query['closed'])) {
            $this->allClosed();
        }

        foreach (array_keys($query) as $filter) {
            $method = $filter;

            if ($filter == 'assigned_to') {
                $method = 'assignedTo';
            }

            if (method_exists(get_called_class(), $method) && !empty($query[$filter])) {
                $this->{$method}();
            } elseif (method_exists(get_called_class(), $method) && empty($query[$filter])) {
                $this->filters[$filter] = ['cond' => true, 'values' => []];
            }
        }
    }

    /**
     * Summary.
     */
    protected function summary()
    {
        $this->likeFilter('summary', 't.summary');
    }

    /**
     * Description / body.
     */
    protected function description()
    {
        $this->likeFilter('description', 't.body');
    }

    /**
     * Owner / reporter / user.
     */
    protected function owner()
    {
        $this->filterIn('owner', 'u.name');
    }

    /**
     * Assigned to / assignee
     */
    public function assignedTo()
    {
        $this->filterIn('assigned_to', 'at.name');
    }

    /**
     * Components.
     */
    protected function component()
    {
        $this->filterIn('component', 'c.name');
    }

    /**
     * Milestones.
     */
    protected function milestone()
    {
        $this->filterIn('milestone', 'm.slug');
    }

    /**
     * Versions.
     */
    protected function version()
    {
        $this->filterIn('version', 'v.slug');
    }

    /**
     * Statuses.
     */
    protected function status()
    {
        $this->filterIn('status', 's.name');
    }

    /**
     * Types.
     */
    protected function type()
    {
        $this->filterIn('type', 'tp.name');
    }

    /**
     * Priorities.
     */
    protected function priority()
    {
        $this->filterIn('priority', 'p.name');
    }

    /**
     * Severities.
     */
    protected function severity()
    {
        $this->filterIn('severity', 'sv.name');
    }

    /**
     * Search filter: summary OR body.
     */
    protected function search()
    {
        $info = $this->extract('search');

        $value = $this->quote(str_replace('*', '%', "%{$info['values']}%"));

        $expr = $this->expr->orX(
            $this->expr->like('t.summary', $value),
            $this->expr->like('t.body', $value)
        );

        $this->builder->andWhere($expr);

        $this->filters['search'] = $info;
    }

    /**
     * All open statuses.
     */
    protected function allOpen()
    {
        $statuses = queryBuilder()->select('id', 'name')->from(PREFIX . 'statuses')
            ->where('status >= 1')
            ->execute();

        $ids = [];
        $names = [];
        foreach ($statuses->fetchAll() as $status) {
            $ids[] = $status['id'];
            $names[] = $status['name'];
        }

        $this->builder->andWhere(
            $this->expr->in('t.status_id', $ids)
        );

        $this->filters['status'] = ['cond' => true, 'values' => $names];
    }

    /**
     * All started statuses.
     */
    protected function allStarted()
    {
        $statuses = queryBuilder()->select('id', 'name')->from(PREFIX . 'statuses')
            ->where('status = 2')
            ->execute();

        $ids = [];
        $names = [];
        foreach ($statuses->fetchAll() as $status) {
            $ids[] = $status['id'];
            $names[] = $status['name'];
        }

        $this->builder->andWhere(
            $this->expr->in('t.status_id', $ids)
        );

        $this->filters['status'] = ['cond' => true, 'values' => $names];
    }

    /**
     * All closed statuses.
     */
    protected function allClosed()
    {
        $statuses = queryBuilder()->select('id', 'name')->from(PREFIX . 'statuses')
            ->where('status <= 0')
            ->execute();

        $ids = [];
        $names = [];
        foreach ($statuses->fetchAll() as $status) {
            $ids[] = $status['id'];
            $names[] = $status['name'];
        }

        $this->builder->andWhere(
            $this->expr->in('t.status_id', $ids)
        );

        $this->filters['status'] = ['cond' => true, 'values' => $names];
    }

    /**
     * Get filter information.
     */
    protected function extract($filter)
    {
        if (isset($this->query[$filter])) {
            $info = [
                'cond' => $this->query[$filter][0] == '!' ? false : true
            ];
            $info['values'] = $info['cond'] ? $this->query[$filter] : substr($this->query[$filter], 1);

            return $info;
        }

        return false;
    }

    /**
     * Quote string.
     */
    protected function quote($string)
    {
        return $GLOBALS['db']->quote($string);
    }

    /**
     * Filter field with `IN`.
     *
     * @param string $filterName
     * @param string $field
     */
    protected function filterIn($filterName, $field)
    {
        $info = $this->extract($filterName);

        $info['values'] = explode(',', $info['values']);
        $values = array_map([$this, 'quote'], $info['values']);

        if ($info['cond']) {
            $expr = $this->expr->in($field, $values);
        } else {
            $expr = $this->expr->notIn($field, $values);
        }

        $this->builder->andWhere($expr);

        $this->filters[$filterName] = $info;
    }

    /**
     * Filter field with `LIKE`.
     *
     * @param string $filterName
     * @param string $field
     */
    protected function likeFilter($filterName, $field)
    {
        $info = $this->extract($filterName);

        $info['values'] = explode(',', str_replace('*', '%', $info['values']));

        foreach ($info['values'] as $value) {
            if ($info['cond']) {
                $expr = $this->expr->like($field, "'%{$value}%'");
            } else {
                $expr = $this->expr->notLike($field, "'%{$value}%'");
            }
        }

        $this->builder->andWhere($expr);

        $this->filters[$filterName] = $info;
    }
}
