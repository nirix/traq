<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Traq.io
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

namespace traq\models;

use avalon\database\Model;

/**
 * Ticket relationship model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 * @since 3.4.0
 */
class TicketRelationship extends Model
{
    protected static $_name = 'ticket_relationships';
    protected static $_properties = array(
        'id',
        'ticket_id',
        'related_ticket_id'
    );

    protected static $_belongs_to = array(
        'ticket',
        'related_ticket' => array('model' => 'Ticket', 'column' => 'related_ticket_id')
    );

    public function is_valid()
    {
        return true;
    }
}
