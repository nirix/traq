<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

namespace traq\controllers\admin;

use avalon\http\Request;
use avalon\output\View;

use traq\models\Type;

/**
 * Admin Types controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Types extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('types'));
    }

    public function action_index()
    {
        $types = Type::fetch_all();
        View::set('types', $types);
    }

    /**
     * New type page.
     */
    public function action_new()
    {
        // Create a new type object
        $type = new Type();

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Set the information
            $type->set(array(
                'name'      => Request::post('name'),
                'bullet'    => Request::post('bullet'),
                'changelog' => Request::post('changelog', 0),
                'template'  => Request::post('template'),
            ));

            // Check if the data is valid
            if ($type->is_valid()) {
                // Save and redirect
                $type->save();
                if ($this->is_api) {
                    return \API::response(1, array('type' => $type));
                } else {
                    Request::redirectTo('/admin/tickets/types');
                }
            }
        }

        // Send the data to the view
        View::set('type', $type);
    }

    /**
     * Edit type.
     *
     * @param integer $id
     */
    public function action_edit($id)
    {
        // Find the type
        $type = Type::find($id);

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Update the information
            $type->set(array(
                'name'      => Request::post('name'), $type->name,
                'bullet'    => Request::post('bullet', $type->bullet),
                'template'  => Request::post('template', $type->template),
            ));

            // Set changelog value
            if ($this->is_api) {
                $type->changelog = Request::post('changelog', $type->changelog);
            } else {
                $type->changelog = isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0;
            }

            // Check if the data is valid
            if ($type->is_valid()) {
                // Save and redirect.
                $type->save();
                if ($this->is_api) {
                    return \API::response(1, array('type' => $type));
                } else {
                    Request::redirectTo('/admin/tickets/types');
                }
            }
        }

        // Send the data to the view.
        View::set('type', $type);
    }

    /**
     * Delete type.
     *
     * @param integer $id
     */
    public function action_delete($id)
    {
        // Find the type, delete and redirect.
        $type = Type::find($id)->delete();
        if ($this->is_api) {
            return \API::response(1);
        } else {
            Request::redirectTo('/admin/tickets/types');
        }
    }
}
