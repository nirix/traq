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

        $options = array();

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
}
