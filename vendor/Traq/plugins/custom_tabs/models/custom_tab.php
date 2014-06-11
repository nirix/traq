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

namespace CustomTabs\models;

/**
 * Custom tabs model.
 *
 * @author Jack P.
 * @since 3.0.7
 * @package CustomTabs
 * @subpackage Models
 */
class CustomTab extends \avalon\database\model
{
    protected static $_name = 'custom_tabs';
    protected static $_properties = array(
        'id',
        'label',
        'url',
        'project_id',
        'groups',
        'display_order'
    );

    public function is_valid()
    {
        if (empty($this->_data['label'])) {
            $this->errors['label'] = l('errors.label_blank');
        }

        if (empty($this->_data['url'])) {
            $this->errors['url'] = l('errors.url_empty');
        }

        return !count($this->errors) > 0;
    }
}
