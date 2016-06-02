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

namespace Traq\Controllers;

use Avalon\Http\Request;
use Avalon\Helpers\Pagination;
use Traq\Helpers\Ticketlist;
use Traq\Helpers\TicketFilters;
use Traq\Helpers\TicketFilterQuery;
use Traq\Models\CustomField;

/**
 * Ticket listing controller.
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 4.0.0
 */
class TicketListing extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->addCrumb($this->translate('tickets'), $this->generateUrl('tickets'));

        // Custom fields
        $this->customFields = CustomField::forProject($this->currentProject['id']);
        $this->set('customFields', $this->customFields);
    }

    /**
     * Ticket listing.
     */
    public function indexAction()
    {
        // Only get the current projects tickets
        $tickets = ticketQuery()
            ->where('t.project_id = ?')
            ->setParameter(0, $this->currentProject['id']);

        // Sort tickets by the projects sorting setting or by the users selection
        $this->sortTickets($tickets);

        // Filter tickets
        $filter = new TicketFilterQuery($tickets, $this->getFilters());
        $queryString = $filter->query;

        // Paginate tickets
        $pagination = new Pagination(
            Request::$query->get('page', 1),
            setting('tickets_per_page'),
            $tickets->execute()->rowCount(),
            $filter->query
        );

        if ($pagination->paginate) {
            $tickets->setFirstResult($pagination->limit);
            $tickets->setMaxResults(setting('tickets_per_page'));
        }

        // Fetch all tickets
        $tickets = $tickets->execute()->fetchAll();

        return $this->respondTo(function ($format) use ($filter, $tickets, $pagination) {
            if ($format == 'html') {
                return $this->render('ticket_listing/index.phtml', [
                    'filters'     => $filter->filters,
                    'columns'     => $this->getColumns(),
                    'tickets'     => $tickets,
                    'pagination'  => $pagination
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse([
                    'page' => (int) $pagination->page,
                    'total_pages' => (int) $pagination->totalPages,
                    'filters' => $filter->filters,
                    'tickets' => $tickets
                ]);
            }
        });
    }

    /**
     * Sort tickets.
     */
    protected function sortTickets($tickets)
    {
        $sorting = explode('.', $this->currentProject['default_ticket_sorting']);

        if ($sorting[0] == 'priority') {
            $sortColumn = 'priority_id';
        } elseif ($sorting[0] == 'ticket_id') {
            $sortColumn = 'ticket_id';
        }

        $tickets->orderBy("t.{$sortColumn}, t.ticket_id", $sorting[1]);
    }

    /**
     * Get ticket filters.
     *
     * @return array
     */
    protected function getFilters()
    {
        $allowedFilters = array_keys(TicketFilters::filtersFor($this->currentProject));
        $allowedFilters[] = 'open';
        $allowedFilters[] = 'started';
        $allowedFilters[] = 'closed';

        $query = [];
        foreach ($allowedFilters as $filter) {
            if (Request::$query->has($filter)) {
                $query[$filter] = Request::$query->get($filter);
            }
        }

        if (!count($query) && isset($_SESSION['ticketFilters'])) {
            $query = json_decode($_SESSION['ticketFilters'], true);
        }

        return $query;
    }

    /**
     * Set ticket filters.
     */
    public function setFiltersAction()
    {
        $queryString = [];

        $filters = Request::$post->get('filters', [], false);

        // Add filter
        if ($newFilter = Request::$post->get('new_filter') and $newFilter !== '') {
            if (!isset($filters[$newFilter])) {
                $filters[$newFilter] = [
                    'prefix' => '',
                    'values' => []
                ];
            } else {
                $filters[$newFilter]['values'][] = '';
            }
        }

        foreach ($filters as $name => $filter) {
            $filter['prefix'] = $filter['prefix'] == '-' ? '!' : '';

            // Is this a filter?
            if (!in_array($name, array_keys(TicketFilters::filtersFor($this->currentProject)))) {
                continue;
            }

            if (!isset($filter['values'])) {
                $filter['values'] = [];
            }

            if ($field = CustomField::find('slug', $name)) {
                $queryString[$name] = $filter['prefix'] . implode(',', $filter['values']);
            } else {
                $queryString[$name] =  $filter['prefix'] . implode(',', $filter['values']);
            }
        }

        return $this->redirect(
            $this->generateUrl('tickets', [
                'pslug' => $this->currentProject['slug']
            ]) . '?' . Request::buildQueryString($queryString, false)
        );
    }

    /**
     * Set columns.
     */
    public function setColumnsAction()
    {
        $this->getColumns();
        return $this->redirect(
            $this->generateUrl('tickets', [
                'pslug' => $this->currentProject['slug']
            ]) . '?' . $_SERVER['QUERY_STRING']
        );
    }

    /**
     * Get columns.
     */
    protected function getColumns()
    {
        $allowedColumns = Ticketlist::allowedColumns();

        // Add custom fields
        foreach ($this->customFields as $field) {
            $allowedColumns[] = $field->id;
        }

        if (Request::$method == 'POST' && Request::$post->has('update_columns')) {
            // Columns from POST
            $newColumns = [];

            foreach (Request::$post->get('columns', [], false) as $column) {
                $newColumns[] = $column;
            }

            $_SESSION['columns'] = Request::$post['columns'] = $newColumns;
            return $newColumns;
        } elseif (isset(Request::$query['columns'])) {
            // Columns from request
            $columns = [];

            foreach (explode(',', Request::$post->get('columns', [], false)) as $column) {
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
}
