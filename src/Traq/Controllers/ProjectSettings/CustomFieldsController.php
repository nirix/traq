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

namespace Traq\Controllers\ProjectSettings;

use Avalon\Http\Request;
use Avalon\Http\Response;
use Traq\Models\CustomField;

/**
 * Custom fields controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class CustomFieldsController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('custom_fields'));
    }

    public function index(): Response
    {
        $customFields = CustomField::select()->where('project_id', $this->project->id)->exec()->fetch_all();

        if ($this->isJson) {
            return $this->json([
                'customFields' => $customFields,
            ]);
        }

        return $this->render('project_settings/custom_fields/index.phtml', [
            'customFields' => $customFields,
        ]);
    }

    /**
     * New field page.
     */
    public function new(): Response
    {
        // Create field
        $field = new CustomField(array(
            'type'  => 'text',
            'regex' => '^(.*)$'
        ));

        // Check if the form has been submitted
        if (Request::method() === 'POST') {
            $data = [];

            // Loop over properties
            foreach (CustomField::properties() as $property) {
                // Check if it's set and not empty
                if (isset(Request::$post[$property])) {
                    $data[$property] = Request::$post[$property];
                }
            }

            if ($data['min_length'] == '') {
                $data['min_length'] = 0;
            }

            if ($data['max_length'] == '') {
                $data['max_length'] = 255;
            }

            // Is required?
            if (!isset(Request::$post['is_required'])) {
                $data['is_required'] = 0;
            }

            // Project ID
            $data['project_id'] = $this->project->id;

            // Set field properties
            $field->set($data);

            // Save and redirect
            if ($field->save()) {
                if ($this->isApi) {
                    return $this->json([
                        'success' => true,
                        'field' => $field,
                    ]);
                } else {
                    return $this->redirectTo($this->project->href('settings/custom_fields'));
                }
            }
        }

        $view = Request::get('overlay') === 'true' ? 'new.overlay.phtml' : 'new.phtml';

        // Send field object to view
        return $this->render("project_settings/custom_fields/{$view}", [
            'field' => $field,
        ]);
    }

    /**
     * Edit field page.
     *
     * @param integer $id
     */
    public function edit(int $id): Response
    {
        // Get field
        $field = CustomField::find($id);

        // Verify project
        if ($field->project_id !== $this->project->id) {
            return $this->renderNoPermission();
        }

        // Check if the form has been submitted
        if (Request::method() === 'POST') {
            $data = [];

            // Loop over properties
            foreach (CustomField::properties() as $property) {
                // Check if it's set and not empty
                if (isset(Request::$post[$property])) {
                    $data[$property] = Request::$post[$property];
                }
            }

            if ($data['min_length'] == '') {
                $data['min_length'] = 0;
            }

            if ($data['max_length'] == '') {
                $data['max_length'] = 255;
            }

            if ($this->isApi) {
                $data['is_required'] = Request::get('is_required', $field->is_required);
                $data['multiple'] = Request::get('multiple', $field->multiple);
            } else {
                $data['is_required'] = Request::get('is_required', 0);
                $data['multiple'] = Request::get('multiple', 0);
            }

            // Set field properties
            $field->set($data);

            // Save and redirect
            if ($field->save()) {
                if ($this->isApi) {
                    return $this->json([
                        'field' => $field,
                    ]);
                } else {
                    return $this->redirectTo($this->project->href('settings/custom_fields'));
                }
            } else {
                return $this->json([
                    'success' => false,
                    'errors' => $field->errors(),
                ]);
            }
        }

        $view = Request::get('overlay') === 'true' ? 'edit.overlay.phtml' : 'edit.phtml';

        // Send field object to view
        return $this->render("project_settings/custom_fields/{$view}", [
            'field' => $field,
        ]);
    }

    /**
     * Delete field.
     */
    public function delete(int $id): Response
    {
        // Find field
        $field = CustomField::find($id);

        // Verify project
        if ($field->project_id !== $this->project->id) {
            return $this->renderNoPermission();
        }

        // Delete and redirect
        $field->delete();

        if ($this->isApi) {
            return $this->json([
                'success' => true,
            ]);
        } else {
            return $this->redirectTo($this->project->href('settings/custom_fields'));
        }
    }
}
