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
use Traq\Controllers\Admin\AppController;
use Traq\Models\ProjectRole;

/**
 * Admin Project Roles controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ProjectRolesController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('roles'));
    }

    /**
     * Role listing page.
     */
    public function index(): Response
    {
        $roles = ProjectRole::fetchAll();

        if ($this->isJson) {
            return $this->json($roles);
        }

        return $this->render('admin/project_roles/index.phtml', ['roles' => $roles]);
    }

    /**
     * New role page.
     */
    public function new(): Response
    {
        // Create a new role object
        $role = new ProjectRole;

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Set the role name
            $role->name       = Request::get('name');
            $role->assignable = Request::get('assignable');
            $role->project_id = Request::get('project');

            // Validate the data
            if ($role->is_valid()) {
                $role->save();

                if ($this->isApi) {
                    return $this->json(['role' => $role]);
                } else {
                    return $this->redirectTo('/admin/roles');
                }
            }
        }

        if (Request::get('overlay') === 'true') {
            $view = 'new.overlay.phtml';
        } else {
            $view = 'new.phtml';
        }

        return $this->render("admin/project_roles/{$view}", ['role' => $role]);
    }

    /**
     * Edit role page.
     */
    public function edit(int $id): Response
    {
        // Fetch the role
        $role = ProjectRole::find($id);

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Update the role name
            $role->name       = Request::get('name', $role->name);
            $role->assignable = Request::get('assignable', 0);
            $role->project_id = Request::get('project', $role->project_id);

            // Validate the data
            if ($role->is_valid()) {
                $role->save();

                if ($this->isApi) {
                    return $this->json(['role' => $role]);
                } else {
                    return $this->redirectTo('/admin/roles');
                }
            }
        }

        if (Request::get('overlay') === 'true') {
            $view = 'edit.overlay.phtml';
        } else {
            $view = 'edit.phtml';
        }

        return $this->render("admin/project_roles/{$view}", ['role' => $role]);
    }

    /**
     * Delete role page.
     */
    public function delete(int $id): Response
    {
        // Fetch and delete the role, then redirect
        $role = ProjectRole::find($id)->delete();

        if ($this->isApi) {
            return $this->json(['success' => true]);
        } else {
            return $this->redirectTo('/admin/roles');
        }
    }
}
