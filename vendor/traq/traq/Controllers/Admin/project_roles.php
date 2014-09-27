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

use traq\models\ProjectRole;

/**
 * Admin Project Roles controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ProjectRoles extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('roles'));
    }

    /**
     * Role listing page.
     */
    public function action_index()
    {
        View::set('roles', ProjectRole::fetch_all());
    }

    /**
     * New role page.
     */
    public function action_new()
    {
        // Create a new role object
        $role = new ProjectRole;

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Set the role name
            $role->name       = Request::post('name');
            $role->assignable = Request::post('assignable');
            $role->project_id = Request::post('project');

            // Validate the data
            if ($role->is_valid()) {
                $role->save();

                if ($this->is_api) {
                    return \API::response(1, array('role' => $role));
                } else {
                    Request::redirectTo('/admin/roles');
                }
            }
        }

        View::set('role', $role);
    }

    /**
     * Edit role page.
     */
    public function action_edit($id)
    {
        // Fetch the role
        $role = ProjectRole::find($id);

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Update the role name
            $role->name       = Request::post('name', $role->name);
            $role->assignable = Request::post('assignable', 0);
            $role->project_id = Request::post('project', $role->project_id);

            // Validate the data
            if ($role->is_valid()) {
                $role->save();

                if ($this->is_api) {
                    return \API::response(1, array('role' => $role));
                } else {
                    Request::redirectTo('/admin/roles');
                }
            }
        }

        View::set('role', $role);
    }

    /**
     * Delete role page.
     */
    public function action_delete($id)
    {
        // Fetch and delete the role, then redirect
        $role = ProjectRole::find($id)->delete();

        if ($this->is_api) {
            return \API::response(1);
        } else {
            Request::redirectTo('/admin/roles');
        }
    }
}
