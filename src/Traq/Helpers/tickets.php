<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Traq.io
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

use avalon\helpers\Time;
use Traq\Models\CustomField;

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
        'priority',
        'severity',
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
        'summary'     => l('summary'),
        'description' => l('description'),
        'owner'       => l('owner'),
        'assigned_to' => l('assigned_to'),
        'component'   => l('component'),
        'milestone'   => l('milestone'),
        'version'     => l('version'),
        'status'      => l('status'),
        'type'        => l('type'),
        'priority'    => l('priority'),
        'severity'    => l('severity'),
        'search'      => l('search')
    );

    // Run plugin hook
    FishHook::run('function:ticket_filters', array(&$filters));

    return $filters;
}

/**
 * Returns an array of custom field ticket filters for the specified project.
 *
 * @param object $project
 *
 * @return array
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function custom_field_filters_for($project)
{
    static $filters = array();

    if (count($filters)) {
        return $filters;
    }

    foreach (CustomField::for_project($project->id) as $field) {
        $filters[$field->slug] = $field->name;
    }

    return $filters;
}

/**
 * Returns an array of all ticket filters, including
 * custom fields.
 *
 * @param object $project
 *
 * @return array
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function ticket_filters_for($project)
{
    static $filters = [];

    if (count($filters)) {
        return $filters;
    }

    $filters = ticket_filters();

    foreach (custom_field_filters_for($project) as $field => $name) {
        $filters[$field] = $name;
    }

    return $filters;
}

/**
 * Returns a list of available ticket history sorting options
 * for use with the `Form::select` helper.
 *
 * @return array
 *
 * @author Jack P.
 * @package Traq
 */
function ticket_history_sorting_options()
{
    return array(
        array('label' => l('oldest_first'), 'value' => 'oldest_first'),
        array('label' => l('newest_first'), 'value' => 'newest_first')
    );
}

/**
 * Returns an array of columns allowed to be used for sorting the
 * ticket listing page to be used with the `Form::select` helper.
 *
 * @return array
 *
 * @author Jack P.
 * @package Traq
 */
function ticket_sorting_select_options()
{
    $options = array();

    // This is hackish and needs to be fixed in 4.0
    $options[l('ascending')] = array();
    $options[l('descending')] = array();

    foreach (ticketlist_allowed_columns() as $column) {
        $options[l('ascending')][] = array('label' => l($column), 'value' => "{$column}.asc");
        $options[l('descending')][] = array('label' => l($column), 'value' => "{$column}.desc");
    }

    return $options;
}

/**
 * Checks the ticket creation delay against the last created
 * ticket.
 *
 * TODO: In 4.0, this will be moved into the new action limiting feature.
 *
 * @return boolean
 */
function check_ticket_creation_delay($ticket)
{
    if (isset($_SESSION['last_ticket_creation']) and $_SESSION['last_ticket_creation'] > (time() - settings('ticket_creation_delay'))) {
        $ticket->_add_error(
            'ticket_creation_delay',
            l('errors.you_must_wait_x', Time::difference_in_words($_SESSION['last_ticket_creation'] + settings('ticket_creation_delay')))
        );
        return false;
    }

    return true;
}
