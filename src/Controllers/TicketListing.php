<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
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

namespace Traq\Controllers;

use Avalon\Http\Request;
use Avalon\Helpers\Pagination;
use Traq\Helpers\TicketFilterQuery;
use Traq\Helpers\Ticketlist;
use Traq\Helpers\TicketFilters;
use Traq\Models\CustomField;

/**
 * Ticket listing controller.
 *
 * @author Jack P.
 * @since 4.0.0
 */
class TicketListing extends AppController
{
    public function __construct()
    {
        parent::__construct();

        // Set the title and load the helper
        $this->title($this->translate('issues'));

        // Custom fields
        $this->customFields = CustomField::forProject($this->project->id);
        $this->set('customFields', $this->customFields);
    }

    /**
     * Ticket listing.
     */
    public function indexAction()
    {
        // Atom feed
        $this->feeds[] = [
            Request::requestUri() . ".atom",
            $this->translate('x_ticket_feed', [$this->project->name])
        ];

        // Create ticket filter query
        $filterQuery = new TicketFilterQuery($this->project);

        $ticketFilters = array_keys(TicketFilters::filtersFor($this->project));

        // Process filters from request
        foreach (Request::$query as $filter => $value) {
            if (in_array($filter, $ticketFilters)) {
                $filterQuery->process($filter, $value);
            }
        }

        // Process filters from the session
        if (!count($filterQuery->filters())
        && isset($_SESSION['ticket_filters'])
        && isset($_SESSION['ticket_filters'][$this->project->id])) {
            $filterValues = json_decode($_SESSION['ticket_filters'][$this->project->id], true);
            foreach ($filterValues as $filter => $value) {
                if (in_array($filter, $ticketFilters)) {
                    $filterQuery->process($filter, $value);
                }
            }
        } else {
            $_SESSION['ticket_filters'][$this->project->id] = json_encode(Request::$query);
        }

        // Get query builder
        $tickets = $filterQuery->builder();

        $this->set('filters', $filterQuery->filters() ?: []);

        return $this->respondTo(function ($format) use ($tickets) {
            if ($format == 'html' || $format == 'json') {
                $sorting = Ticketlist::sortOrder($this->project->default_ticket_sorting);
                $tickets->orderBy($sorting[0], $sorting[1]);
            }

            if ($format == 'html') {
                // Paginate tickets
                $pagination = new Pagination(
                    Request::request('page', 1),
                    $this->setting('tickets_per_page'),
                    $tickets->execute()->rowCount()
                );

                if ($pagination->paginate) {
                    $tickets->setFirstResult($pagination->limit);
                    $tickets->setMaxResults($this->setting('tickets_per_page'));
                }

                return $this->render('ticket_listing/index.phtml', [
                    'tickets'    => $tickets->fetchAll(),
                    'pagination' => $pagination,
                    'columns'    => $this->getColumns()
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($tickets->fetchAll());
            } elseif ($format == 'atom') {
                throw new \Exception("Not implemented");
            }
        });
    }

    /**
     * Get columns for the ticket listing page.
     *
     * @return array
     */
    protected function getColumns()
    {
        $allowedColumns = Ticketlist::allowedColumns();

        // Add custom fields
        foreach ($this->customFields as $field) {
            $allowedColumns[] = $field->id;
        }

        if (Request::method() == 'POST' && Request::post('update_columns')) {
            // Columns from POST
            $newColumns = [];

            foreach (Request::$post['columns'] as $column) {
                $newColumns[] = $column;
            }

            $_SESSION['columns'] = Request::$request['columns'] = $newColumns;
            return $newColumns;
        } elseif (isset(Request::$query['columns'])) {
            // Columns from request
            $columns = [];

            foreach (explode(',', Request::$request['columns']) as $column) {
                // Make sure it's a valid column
                if (in_array($column, $allowedColumns)) {
                    $columns[] = $column;
                }
            }

            return $columns;
        } elseif (isset($_SESSION['columns'])) {
            // Columns from session
            return $_SESSION['columns'];
        } else {
            // Use default columns
            return Ticketlist::defaultColumns();
        }
    }

    /**
     * Set columns to be displayed on the ticket listing page.
     */
    public function setColumnsAction()
    {
        $this->getColumns();
        return $this->redirect($this->project->href('issues') . Request::buildQueryString(null, false));
    }


    /**
     * Processes the ticket filters form and builds the query string.
     */
    public function updateFiltersAction()
    {
        $queryString = [];

        // Add filter
        if ($newFilter = Request::post('new_filter') and $newFilter !== '') {
            if (!isset(Request::$post['filters'][$newFilter])) {
                Request::$post['filters'][$newFilter] = [
                    'prefix' => '',
                    'values' => []
                ];
            } else {
                Request::$post['filters'][$newFilter]['values'][] = '';
            }
        }

        foreach (Request::post('filters', []) as $name => $filter) {
            if (!in_array($name, array_keys(TicketFilters::filtersFor($this->project)))) {
                continue;
            }

            if (!isset($filter['values'])) {
                $filter['values'] = [];
            }

            switch ($name) {
                case 'summary':
                case 'description':
                case 'owner':
                case 'assigned_to':
                    $queryString[$name] = $filter['prefix'] . implode(',', $filter['values']);
                    break;

                case 'milestone':
                case 'version':
                case 'type':
                case 'status':
                case 'component':
                case 'priority':
                case 'severity':
                    $class = "\Traq\\Models\\" . ($name == 'version' ? 'Milestone' : ucfirst($name));

                    if ($name == 'milestone' || $name == 'version') {
                        $field = 'slug';
                    } else {
                        $field = 'name';
                    }

                    $values = [];

                    foreach ($filter['values'] as $value) {
                        $values[] = $class::find($value)->{$field};
                    }

                    $queryString[$name] = $filter['prefix'] . implode(',', $values);
                    break;
            }

            if ($field = CustomField::find('slug', $name)) {
                $queryString[$name] = $filter['prefix'] . implode(',', $filter['values']);
            }
        }

        $_SESSION['ticket_filters'] = [];

        return $this->redirect($this->project->href('issues') . Request::buildQueryString($queryString, false));
    }
}
