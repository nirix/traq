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

namespace traq\models;

use avalon\database\Model;

/**
 * Wiki page revision database model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P. <jack@traq.io>
 * @copyright (c) Jack P. <jack@traq.io>
 */
class WikiRevision extends Model
{
    protected static $_name = 'wiki_revisions';
    protected static $_properties = array(
        'id',
        'wiki_page_id',
        'revision',
        'content',
        'user_id',
        'created_at',
        'updated_at'
    );

    protected static $_belongs_to = array(
        'user',
        'wiki_page' => array('model' => 'WikiPage')
    );

    public function is_valid()
    {
        return true;
    }
}
