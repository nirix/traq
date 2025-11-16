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
use Traq\Models\Priority;

/**
 * Priorities controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class PrioritiesController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('priorities'));
    }

    /**
     * priority listing.
     */
    public function index(): Response
    {
        $priorities = Priority::fetchAll();

        if ($this->isJson) {
            return $this->json(['priorities' => $priorities]);
        }

        return $this->render('admin/priorities/index', ['priorities' => $priorities]);
    }

    /**
     * New priority.
     */
    public function new(): Response
    {
        // Create the priority
        $priority = new Priority();

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Set the name
            $priority->set('name', Request::get('name'));

            // Save and redirect
            if ($priority->save()) {
                if ($this->isJson) {
                    return $this->json(['priority' => $priority]);
                } else {
                    return $this->redirectTo('/admin/priorities');
                }
            }
        }

        if (Request::get('overlay') === 'true') {
            $view = 'new.overlay.phtml';
        } else {
            $view = 'new.phtml';
        }

        return $this->render("admin/priorities/{$view}", ['priority' => $priority]);
    }

    /**
     * Edit priority.
     *
     * @param integer $id
     */
    public function edit(int $id): Response
    {
        // Get the priority
        $priority = Priority::find($id);

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Set the name
            $priority->set('name', Request::get('name', $priority->name));

            // Save and redirect
            if ($priority->save()) {
                if ($this->isJson) {
                    return $this->json(['priority' => $priority]);
                } else {
                    return $this->redirectTo('/admin/priorities');
                }
            }
        }

        if (Request::get('overlay') === 'true') {
            $view = 'edit.overlay.phtml';
        } else {
            $view = 'edit.phtml';
        }

        return $this->render("admin/priorities/{$view}", ['priority' => $priority]);
    }

    /**
     * Delete priority.
     *
     * @param integer $id
     */
    public function delete(int $id): Response
    {
        // Find and delete priority
        $priority = Priority::find($id)->delete();

        if ($this->isJson) {
            return $this->json(['success' => true]);
        } else {
            return $this->redirectTo('/admin/priorities');
        }
    }
}
