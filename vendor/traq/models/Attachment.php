<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

namespace traq\models;

use Avalon\Database\Model;

/**
 * Attachment model.
 *
 * @package Traq
 * @subpackage Models
 * @since 3.0
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Attachment extends Model
{
    protected static $_name = 'attachments';
    protected static $_properties = array(
        'id',
        'name',
        'contents',
        'type',
        'size',
        'user_id',
        'ticket_id',
        'created_at'
    );

    protected static $_belongs_to = array('user', 'ticket');

    /**
     * Returns the URL for the attachment.
     */
    public function href($extra = '')
    {
        return "/attachments/{$this->id}/" . create_slug($this->name) . $extra;
    }

    public function is_valid()
    {
        return true;
    }
}
