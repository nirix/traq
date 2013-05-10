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

use avalon\core\Kernel as Avalon;

use traq\models\Type;
use traq\models\Status;
use traq\models\Component;
use traq\models\Priority;
use traq\models\Severity;
use traq\models\CustomField;

/**
 * Returns the URL for sorting the provided ticket column.
 *
 * @param string $column
 *
 * @return string
 */
function ticket_sort_url_for($column) {
    // Get current order
    if (isset(Request::$request['order_by'])) {
        $order = explode('.', Request::$request['order_by']);
    } else {
        return Request::requestUri() . (strlen($_SERVER['QUERY_STRING']) ? '&amp;' : '?') . "order_by={$column}.asc";
    }

    // Are we flipping the current sort?
    if ($order[0] == $column) {
        $query = "{$column}." . (strtolower($order[1]) == 'asc' ? 'desc' : 'asc');
    } else {
        $query = "{$column}.{$order[1]}";
    }

    return str_replace("order_by=". implode('.', $order), "order_by={$query}", Request::requestUri());
}

/**
 * Ticket columns
 *
 * @return array
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function ticket_columns() {
    $columns = array(
        'ticket_id',
        'summary',
        'status',
        'owner',
        'type',
        'component',
        'milestone'
    );
    return $columns;
}

/**
 * Returns the table columns allowed on the listing page.
 *
 * @return array
 */
function ticketlist_allowed_columns()
{
    return array(
        'ticket_id',
        'summary',
        'status',
        'owner',
        'type',
        'component',
        'milestone',
        'assigned_to',
        'created_at',
        'updated_at',
        'votes'
    );
}

/**
 * Returns an array of available ticket filters.
 *
 * @return array
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function ticket_filters()
{
    $filters = array(
        'summary',
        'description',
        'owner',
        'assigned_to',
        'component',
        'milestone',
        'version',
        'status',
        'type',
        'priority',
        'severity'
    );

    // Run plugin hook
    FishHook::run('function:ticket_filters', array(&$filters));

    return $filters;
}

/**
 * Returns an array of available ticket filters
 * formatted for Form::select().
 *
 * @return array
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function ticket_filters_select_options()
{
    $options = array();

    // Add blank option
    $options[] = array('label' => '', 'value' => '');

    // Add ticket filters
    foreach (ticket_filters() as $filter) {
        $options[] = array('label' => l($filter), 'value' => $filter);
    }

    return $options;
}

/**
 * Returns the content for the ticket listing headers.
 *
 * @param string $column The column to get the content for.
 *
 * @return mixed
 */
function ticketlist_header($column) {
    switch ($column) {
        case 'ticket_id':
            return l('id');
            break;

        case 'summary':
        case 'status':
        case 'owner':
        case 'type':
        case 'component':
        case 'milestone':
        case 'assigned_to':
        case 'updates':
        case 'votes':
            return l($column);

        case 'created_at':
            return l('reported');
            break;

        case 'updated_at':
            return l('updated');
            break;
    }

    // If we're still here, it may be a custom field
    if ($column = CustomField::find($column)) {
        return $column->name;
    }

    // Nothing!
    return '';
}

/**
 * Returns the content for the ticket listing field.
 *
 * @param string $column The column to get the content for.
 * @param object $ticket The ticket data object.
 *
 * @return mixed
 */
function ticketlist_data($column, $ticket) {
    switch ($column) {
        // Ticket ID column
        case 'ticket_id':
            return $ticket->ticket_id;
            break;

        // Summary column
        case 'summary':
            return $ticket->summary;
            break;

        // Status column
        case 'status':
            return $ticket->status->name;
            break;

        // Owner / author column
        case 'owner':
            return $ticket->user->username;
            break;

        // Ticket type column
        case 'type':
            return $ticket->type->name;
            break;

        // Component column
        case 'component':
            return $ticket->component ? $ticket->component->name : '';
            break;

        // Milestone column
        case 'milestone':
            return $ticket->milestone ? $ticket->milestone->name : '';
            break;

        // Updates column
        case 'updates':
            return $ticket->history->exec()->row_count();
            break;

        // Created at
        case 'created_at':
            return time_ago($ticket->created_at, false);
            break;

        // Updated at
        case 'updated_at':
            return $ticket->updated_at ? time_ago($ticket->updated_at, false) : l('never');
            break;

        // Votes
        case 'votes':
            return $ticket->votes;
            break;
    }

    // If we're still here, it may be a custom field
    if ($column = CustomField::find($column)) {
        if (isset($ticket->extra['custom_fields'][$column->id])) {
            return $ticket->extra['custom_fields'][$column->id];
        }
    }

    // Nothing!
    return '';
}

/**
 * Returns options for the specific ticket filter.
 *
 * @param string $filter
 *
 * @return array
 */
function ticket_filter_options_for($filter, $project_id = null) {
    switch ($filter) {
        // Milestone options
        case 'milestone':
            $options = Avalon::app()->project->milestone_select_options();
            break;

        // Version options
        case 'version':
            $options = Avalon::app()->project->milestone_select_options('completed');
            break;

        // Type options
        case 'type':
            $options = Type::select_options();
            break;

        // Status options
        case 'status':
            $options = Status::select_options();
            break;

        // Component options
        case 'component':
            $options = Component::select_options($project_id);
            break;

        // Priority options
        case 'priority':
            $options = Priority::select_options();
            break;

        // Priority options
        case 'severity':
            $options = Severity::select_options();
            break;
    }

    return $options;
}

/**
 * Ticket listing sort indicator.
 *
 * @param string $column
 */
function ticketlist_sort_indicator($column)
{
    // Default
    $order = 'priority_id.ASC';

    // Check if order_by is set, and use it if it is
    if (isset(Request::$request['order_by'])) {
        $order = Request::$request['order_by'];
    }

    // Split column and order
    $order = explode('.', $order);

    if ($column == $order[0]) {
        return View::render('tickets/_sort_indicator', array('order' => strtolower($order[1])));
    }
}
