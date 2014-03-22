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

namespace traq\models;

use avalon\database\Model;

/**
 * Custom field value model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class CustomFieldValue extends Model
{
    protected static $_name = 'custom_field_values';
    protected static $_properties = array(
        'id',
        'custom_field_id',
        'ticket_id',
        'value'
    );

    protected static $_filters_before = array(
        'create' => array('_encode'),
        'save'   => array('_encode')
    );

    protected static $_filters_after = array(
        'construct' => array('_decode')
    );

    public function is_valid()
    {
        return true;
    }

    protected function _encode()
    {
        $this->value = json_encode($this->value);
    }

    protected function _decode()
    {
        if (!$this->_is_new()) {
            $this->value = json_decode($this->value, true);
        }
    }
}
