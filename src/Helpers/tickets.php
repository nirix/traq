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
 * Returns an array of available ticket filters
 * formatted for Form::select().
 *
 * @return array
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function ticket_filters_select_options($project = null)
{
    $options = array();

    // Add blank option
    $options[] = array('label' => '', 'value' => '');

    // Ticket filters for a specific project
    if ($project !== null) {
        $filters = ticket_filters_for($project);
    }
    // Default filters
    else {
        $filters = ticket_filters();
    }

    // Add filters
    foreach ($filters as $slug => $name) {
        $options[] = array('label' => $name, 'value' => $slug);
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
 * Checks if the `order_by` query string is set and returns it
 * and falls back to the passed value if it isn't.
 *
 * @param string $fallback
 *
 * @return string
 *
 * @author Jack P.
 * @package Traq
 */
function ticket_sort_order($fallback)
{
    if (isset(Request::$request['order_by'])) {
        return Request::$request['order_by'];
    } else {
        return $fallback;
    }
}

/**
 * Checks the ticket creation delay against the last created
 * ticket.
 *
 * TODO: In 4.0, this will be moved into the new action limiting feature.
 *
 * @return boolean
 */
function check_ticket_creation_delay($ticket) {
    if (isset($_SESSION['last_ticket_creation']) and $_SESSION['last_ticket_creation'] > (time() - settings('ticket_creation_delay'))) {
        $ticket->_add_error(
            'ticket_creation_delay',
            l('errors.you_must_wait_x', Time::difference_in_words($_SESSION['last_ticket_creation'] + settings('ticket_creation_delay')))
        );
        return false;
    }

    return true;
}
