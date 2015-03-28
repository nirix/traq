<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
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
use Traq\Models\ProjectRole;

/**
 * Admin Types controller
 *
 * @author Jack P.
 * @since 4.0.0
 * @package Traq\Controllers
 */
class ProjectRoles extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('roles'));
    }

    /**
     * Roles listing.
     */
    public function indexAction()
    {
        $roles = ProjectRole::all();

        return $this->respondTo(function($format) use ($roles) {
            if ($format == 'html') {
                return $this->render('admin/project_roles/index.phtml', [
                    'roles' => $roles
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($roles);
            }
        });
    }

    /**
     * New role page.
     */
    public function newAction()
    {
        if ($this->isOverlay) {
            return $this->render('admin/project_roles/new.overlay.phtml', ['role' => new ProjectRole]);
        } else {
            return $this->render('admin/project_roles/new.phtml', ['role' => new ProjectRole]);
        }
    }

    /**
     * Create role.
     */
    public function createAction()
    {
        $role = new ProjectRole($this->roleParams());

        if ($role->save()) {
            return $this->respondTo(function($format) use ($role) {
                if ($format == "html") {
                    return $this->redirectTo('admin_project_roles');
                } else {
                    return $this->jsonResponse($role);
                }
            });
        } else {
            return $this->render('admin/project_roles/new.phtml', ['role' => $role]);
        }
    }

    /**
     * Edit role page.
     */
    /**
     * New role page.
     */
    public function editAction($id)
    {
        $role = ProjectRole::find($id);

        if ($this->isOverlay) {
            return $this->render('admin/project_roles/edit.overlay.phtml', ['role' => $role]);
        } else {
            return $this->render('admin/project_roles/edit.phtml', ['role' => $role]);
        }
    }

    /**
     * Save role.
     */
    public function saveAction($id)
    {
        $role = ProjectRole::find($id);
        $role->set($this->roleParams());

        if ($role->save()) {
            return $this->respondTo(function($format) use ($role) {
                if ($format == "html") {
                    return $this->redirectTo('admin_project_roles');
                } else {
                    return $this->jsonResponse($role);
                }
            });
        } else {
            return $this->render('admin/project_roles/edit.phtml', ['role' => $role]);
        }
    }

    /**
     * Delete role page.
     */
    public function destroyAction($id)
    {
        // Find the role, delete and redirect.
        $role = ProjectRole::find($id)->delete();

        return $this->respondTo(function($format) use ($role) {
            if ($format == "html") {
                return $this->redirectTo('admin_project_roles');
            } elseif ($format == "json") {
                return $this->jsonResponse([
                    'deleted' => true,
                    'role'    => $role->toArray()
                ]);
            }
        });
    }

    /**
     * @return array
     */
    protected function roleParams()
    {
        return [
            'name'          => Request::post('name'),
            'is_assignable' => Request::post('is_assignable', false),
            'project_id'    => Request::post('project_id')
        ];
    }
}
