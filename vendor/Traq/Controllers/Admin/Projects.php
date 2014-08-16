<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

use Radium\Action\View;
use Radium\Http\Request;

use Traq\Models\Project;

/**
 * Admin Projects controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq\Controllers
 */
class Projects extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('projects'));
    }

    public function indexAction()
    {
        // Fetch all projects and pass them to the view.
        View::set('projects', Project::all());
    }

    /**
     * Create a new project page.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));
        View::set('project', new Project);
    }

    /**
     * Edit project page.
     */
    public function editAction($project_id)
    {
        $this->title($this->translate('edit'));
        View::set('project', Project::find($project_id));
    }

    /**
     * Delete a project.
     *
     * @param integer $id Project ID.
     */
    public function action_delete($id)
    {
        $project = Project::find('id', $id);
        $project->delete();

        // Is this an API request?
        if ($this->is_api) {
            return API::response(1);
        } else {
            Request::redirectTo('admin/projects');
        }
    }
}
