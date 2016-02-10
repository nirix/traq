<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

namespace Traq\Controllers\ProjectSettings;

use Avalon\Http\Request;
use Traq\Models\CustomField;

/**
 * Custom fields controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\ProjectSettings
 */
class CustomFields extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('custom_fields'));
    }

    /**
     * Custom field listing.
     */
    public function indexAction()
    {
        $fields = CustomField::select()
            ->where('project_id = ?')
            ->orderBy('name', 'ASC')
            ->setParameter(0, $this->currentProject['id'])
            ->fetchAll();

        return $this->render('project_settings/custom_fields/index.phtml', [
            'fields' => $fields
        ]);
    }

    /**
     * New field form.
     */
    public function newAction()
    {
        return $this->render('project_settings/custom_fields/new.phtml', [
            'field' => new CustomField
        ]);
    }

    /**
     * Edit field page.
     *
     * @param integer $id
     */
    public function action_edit($id)
    {
        // Get field
        $field = CustomField::find($id);

        // Verify project
        if ($field->project_id != $this->project->id) {
            return $this->show_no_permission();
        }

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            $data = array();

            // Loop over properties
            foreach (CustomField::properties() as $property) {
                // Check if it's set and not empty
                if (isset(Request::$post[$property])) {
                    $data[$property] = Request::$post[$property];
                }
            }

            if ($this->is_api) {
                $data['is_required'] = Request::post('is_required', $field->is_required);
                $data['multiple'] = Request::post('multiple', $field->multiple);
            } else {
                $data['is_required'] = Request::post('is_required', 0);
                $data['multiple'] = Request::post('multiple', 0);
            }

            // Set field properties
            $field->set($data);

            // Save and redirect
            if ($field->save()) {
                if ($this->is_api) {
                    return \API::response(1, array('field' => $field));
                } else {
                    Request::redirectTo($this->project->href('settings/custom_fields'));
                }
            }
        }

        // Send field object to view
        View::set(compact('field'));
    }

    /**
     * Delete field.
     */
    public function action_delete($id)
    {
        // Find field
        $field = CustomField::find($id);

        // Verify project
        if ($field->project_id != $this->project->id) {
            return $this->show_no_permission();
        }

        // Delete and redirect
        $field->delete();

        if ($this->is_api) {
            return \API::response(1);
        } else {
            Request::redirectTo($this->project->href('settings/custom_fields'));
        }
    }
}
