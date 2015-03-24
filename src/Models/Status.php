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

namespace Traq\Models;

use Avalon\Database\Model;
use Avalon\Language;

/**
 * Status model.
 *
 * @author Jack P.
 */
class Status extends Model
{
    protected static $_validates = array(
        'name' => ['required', 'unique']
    );

    // Data types
    protected static $_dataTypes = [
        'show_on_changelog' => "boolean"
    ];

    /**
     * @return Status[]
     */
    public static function allOpen()
    {
        return static::where('status = ?', 1)->fetchAll();
    }

    /**
     * @return Status[]
     */
    public static function allClosed()
    {
        return static::where('status = ?', 0)->fetchAll();
    }

    /**
     * Returns an array formatted for the Form::select() method.
     *
     * @return array
     */
    public static function selectOptions()
    {
        $open   = Language::translate('open');
        $closed = Language::translate('closed');

        $options = array(
            $open   => array(),
            $closed => array()
        );

        foreach (static::all() as $status) {
            $option = array(
                'label' => $status->name,
                'value' => $status->id
            );

            if ($status->status) {
                $options[$open][] = $option;
            } else {
                $options[$closed][] = $option;
            }
        }

        return $options;
    }
}
