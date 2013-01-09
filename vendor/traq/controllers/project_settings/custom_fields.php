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

namespace traq\controllers\ProjectSettings;

use avalon\http\Request;
use avalon\output\View;

use traq\models\CustomField;

/**
 * Custom fields controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class CustomFields extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('custom_fields'));
    }

    public function action_index()
    {
        View::set('custom_fields', CustomField::fetch_all());
    }

    /**
     * New field page.
     */
    public function action_new()
    {
        // Create field
        $field = new CustomField(array(
            'type'  => 'text',
            'regex' => '(.*)'
        ));

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            $data = array();

            // Loop over properties
            foreach (CustomField::properties() as $property) {
                // Check if it's set and not empty
                if (isset(Request::$post[$property]) and !empty(Request::$post[$property])) {
                    $data[$property] = Request::$post[$property];
                }
            }

            // Set field properties
            $field->set($data);

            // Save and redirect
            if ($field->save()) {
                Request::redirectTo($this->project->href('settings/custom_fields'));
            }
        }

        // Send field object to view
        View::set(compact('field'));
    }
}
