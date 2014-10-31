<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace Traq\Helpers;

use Radium\Language;
use Radium\Http\Request;
use Radium\Helpers\Time;

/**
 * Ticket listing helper
 *
 * @author Jack P.
 * @package Traq\Helpers
 * @since 4.0
 */
class Ticketlist
{
    /**
     * Default ticket columns.
     *
     * @return array
     */
    public static function defaultColumns()
    {
        return [
            'ticket_id',
            'summary',
            'status',
            'owner',
            'type',
            'component',
            'milestone',
            'created_at'
        ];
    }

    /**
     * Returns the table columns allowed on the listing page.
     *
     * @return array
     */
    public static function allowedColumns()
    {
        return [
            'ticket_id',
            'summary',
            'status',
            'owner',
            'type',
            'component',
            'milestone',
            'assigned_to',
            'priority',
            'severity',
            'created_at',
            'updated_at',
            'votes'
        ];
    }

    /**
     * Returns a `Form::select()` compatible array of columns that can be used
     * for ticket sorting.
     *
     * @return array
     */
    public static function sortingSelectOptions()
    {
        $asc  = Language::translate('ascending');
        $desc = Language::translate('descending');

        $options = [];

        // This is hackish and needs to be fixed in 4.0
        $options[$asc]  = [];
        $options[$desc] = [];

        foreach (static::allowedColumns() as $column) {
            $options[$asc][]  = [
                'label' => Language::translate($column),
                'value' => "{$column}.asc"
            ];

            $options[$desc][] = [
                'label' => Language::translate($column),
                'value' => "{$column}.desc"
            ];
        }

        return $options;
    }

    /**
     * Get ticket sorting order.
     *
     * @param string $fallback
     *
     * @return array
     */
    public static function sortOrder($fallback = 'id.asc')
    {
        $order = Request::request('order_by', $fallback);

        // field.direction
        $order = explode('.', $order);

        // Check if we need to do
        // anything with the field.
        switch($order[0]) {
            case 'summary':
            case 'body':
            case 'votes':
            case 'created_at':
            case 'updated_at':
                $property = $order[0];
                break;

            case 'user':
            case 'milestone':
            case 'version':
            case 'component':
            case 'type':
            case 'status':
            case 'priority':
            case 'severity':
            case 'assigned_to':
                $property = "{$order[0]}_id";
                break;

            case 'id':
                $property = "ticket_id";
                break;

            default:
                $property = 'ticket_id';
        }

        if (count($order) === 1) {
            $order[] = 'ASC';
        }

        return [$property, strtolower($order[1]) == 'asc' ? "ASC" : "DESC"];
    }

    /**
     * Returns the URL for sorting the provided ticket column.
     *
     * @param string $column
     *
     * @return string
     */
    public static function sortUrlFor($column)
    {
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
     * Returns the content for the ticket listing headers.
     *
     * @param string $column The column to get the content for.
     *
     * @return mixed
     */
    public static function headerFor($column)
    {
        switch ($column) {
            case 'ticket_id':
                return Language::translate('id');
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
            case 'priority':
            case 'severity':
                return Language::translate($column);

            case 'created_at':
                return Language::translate('created');
                break;

            case 'updated_at':
                return Language::translate('updated');
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
    public static function dataFor($column, $ticket) {
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
                return $ticket->status()->name;
                break;

            // Owner / author column
            case 'owner':
                return $ticket->user()->name;
                break;

            // Assigned to
            case 'assigned_to':
                if ($ticket->assigned_to()) {
                    return $ticket->assigned_to()->name;
                } else {
                    return '';
                }
                break;

            // Ticket type column
            case 'type':
                return $ticket->type()->name;
                break;

            // Component column
            case 'component':
                return $ticket->component() ? $ticket->component()->name : '';
                break;

            // Milestone column
            case 'milestone':
                return $ticket->milestone() ? $ticket->milestone()->name : '';
                break;

            // Updates column
            case 'updates':
                return $ticket->history()->execute()->rowCount();
                break;

            // Created at
            case 'created_at':
                return Language::translate('time.x_ago', [
                    Time::agoInWords($ticket->created_at, false)
                ]);
                break;

            // Updated at
            case 'updated_at':
                if ($ticket->updated_at) {
                    return Language::translate('time.x_ago', [
                        Time::agoInWords($ticket->updated_at, false)
                    ]);
                } else {
                    return Language::translate('never');
                }
                break;

            // Votes
            case 'votes':
                return $ticket->votes;
                break;

            // Priority
            case 'priority':
                return $ticket->priority()->name;
                break;

            // Severity
            case 'severity':
                return $ticket->severity()->name;
                break;
        }

        // If we're still here, it may be a custom field
        if ($value = $ticket->customFieldValue($column)) {
            return $value->value;
        }

        // Nothing!
        return '';
    }
}
