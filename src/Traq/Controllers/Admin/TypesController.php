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

namespace Traq\Controllers\Admin;

use Avalon\Http\Request;
use Avalon\Http\Response;
use Traq\Models\Type;

/**
 * Admin Types controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class TypesController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('types'));
    }

    public function index(): Response
    {
        $types = Type::fetchAll();

        if ($this->isJson) {
            return $this->json(['types' => $types]);
        }

        return $this->render('admin/types/index', ['types' => $types]);
    }

    /**
     * New type page.
     */
    public function new(): Response
    {
        // Create a new type object
        $type = new Type();

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Set the information
            $type->set(array(
                'name'      => Request::get('name'),
                'bullet'    => Request::get('bullet'),
                'changelog' => Request::get('changelog', 0),
                'template'  => Request::get('template'),
            ));

            // Check if the data is valid
            if ($type->is_valid()) {
                // Save and redirect
                $type->save();
                if ($this->isJson) {
                    return $this->json(['type' => $type]);
                } else {
                    return $this->redirectTo('/admin/tickets/types');
                }
            }
        }

        // Send the data to the view
        if (Request::get('overlay') === 'true') {
            $view = 'new.overlay.phtml';
        } else {
            $view = 'new.phtml';
        }

        return $this->render("admin/types/{$view}", ['type' => $type]);
    }

    /**
     * Edit type.
     *
     * @param integer $id
     */
    public function edit(int $id): Response
    {
        // Find the type
        $type = Type::find($id);

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Update the information
            $type->set(array(
                'name'      => Request::get('name'),
                $type->name,
                'bullet'    => Request::get('bullet', $type->bullet),
                'template'  => Request::get('template', $type->template),
            ));

            // Set changelog value
            if ($this->isJson) {
                $type->changelog = Request::get('changelog', $type->changelog);
            } else {
                $type->changelog = isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0;
            }

            // Check if the data is valid
            if ($type->is_valid()) {
                // Save and redirect.
                $type->save();
                if ($this->isJson) {
                    return $this->json(['type' => $type]);
                } else {
                    return $this->redirectTo('/admin/tickets/types');
                }
            }
        }

        // Send the data to the view.
        if (Request::get('overlay') === 'true') {
            $view = 'edit.overlay.phtml';
        } else {
            $view = 'edit.phtml';
        }

        return $this->render("admin/types/{$view}", ['type' => $type]);
    }

    /**
     * Delete type.
     *
     * @param integer $id
     */
    public function delete(int $id): Response
    {
        // Find the type, delete and redirect.
        $type = Type::find($id)->delete();
        if ($this->isJson) {
            return $this->json(['success' => true]);
        } else {
            return $this->redirectTo('/admin/tickets/types');
        }
    }
}
