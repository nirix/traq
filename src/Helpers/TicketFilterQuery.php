<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
 * Copyright (C) 2012-2015 Traq.io
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

/**
 * Ticket filtering.
 *
 * @package Traq\Helpers
 * @author Jack P.
 * @since 3.0.0
 */
class TicketFilterQuery
{
    protected $builder;
    protected $expr;
    public $query;
    public $filters = [];

    public function __construct($builder, $query)
    {
        $this->builder = $builder;
        $this->expr    = $builder->expr();
        $this->query   = $query;

        $this->filter();
    }

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

        foreach (array_keys($query) as $method) {
            if (method_exists(get_called_class(), $method) && !empty($query[$method])) {
                $this->{$method}();
            }
        }
    }

    protected function milestone()
    {
        $info = $this->extract('milestone');

        $info['value'] = explode(',', $info['value']);
        $values = array_map([$this, 'quote'], $info['value']);

        foreach ($values as $slug) {
            if ($info['cond']) {
                $expr = $this->expr->in('m.slug', $values);
            } else {
                $expr = $this->expr->notIn('m.slug', $values);
            }
        }

        $this->builder->andWhere($expr);

        $this->filters['milestone'] = $info;
    }

    protected function allOpen()
    {
        $statuses = queryBuilder()->select('id')->from(PREFIX . 'statuses')
            ->where('status >= 1')
            ->execute();

        $ids = [];
        foreach ($statuses->fetchAll() as $status) {
            $ids[] = $status['id'];
        }

        $this->builder->andWhere(
            $this->expr->in('t.status_id', $ids)
        );

        $this->filters['status'] = ['cond' => true, 'value' => $ids];
    }

    protected function allStarted()
    {
        $statuses = queryBuilder()->select('id')->from(PREFIX . 'statuses')
            ->where('status = 2')
            ->execute();

        $ids = [];
        foreach ($statuses->fetchAll() as $status) {
            $ids[] = $status['id'];
        }

        $this->builder->andWhere(
            $this->expr->in('t.status_id', $ids)
        );

        $this->filters['status'] = ['cond' => true, 'value' => $ids];
    }

    protected function allClosed()
    {
        $statuses = queryBuilder()->select('id')->from(PREFIX . 'statuses')
            ->where('status <= 0')
            ->execute();

        $ids = [];
        foreach ($statuses->fetchAll() as $status) {
            $ids[] = $status['id'];
        }

        $this->builder->andWhere(
            $this->expr->in('t.status_id', $ids)
        );

        $this->filters['status'] = ['cond' => true, 'value' => $ids];
    }

    protected function extract($filter)
    {
        if (isset($this->query[$filter])) {
            $info = [
                'cond' => $this->query[$filter][0] == '!' ? false : true
            ];
            $info['value'] = $info['cond'] ? $this->query[$filter] : substr($this->query[$filter], 1);

            return $info;
        }

        return false;
    }

    protected function quote($string)
    {
        return $GLOBALS['db']->quote($string);
    }
}
