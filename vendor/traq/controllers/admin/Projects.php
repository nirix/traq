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

namespace traq\controllers\admin;

use avalon\http\Request;
use avalon\output\View;
use traq\helpers\API;
use traq\models\Project;

/**
 * Admin Projects controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Projects extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title(l('projects'));
    }

    public function action_index()
    {
        // Fetch all projects and pass them to the view.
        $projects = Project::fetch_all();
        View::set('projects', $projects);
    }

    /**
     * Create a new project page.
     */
    public function action_new()
    {
        $this->title(l('new'));

        $project = new Project;

        if (Request::method() == 'POST') {
            $project->set(array(
                'name'         => Request::get('name'),
                'slug'         => Request::get('slug'),
                'codename'     => Request::get('codename'),
                'info'         => Request::get('info'),
                'enable_wiki'  => (isset(Request::$post['enable_wiki']) ? Request::$post['enable_wiki'] : 0),
                'default_ticket_type_id' => Request::get('default_ticket_type_id'),
                'default_ticket_sorting' => Request::get('default_ticket_sorting'),
                'displayorder' => Request::get('displayorder', 0)
            ));

            if (!is_numeric($project->displayorder)) {
                $project->displayorder = 0;
            }

            // Save project
            if ($project->save()) {
                // Is this an API request?
                if ($this->isAapi) {
                    // Return JSON formatted response
                    return API::response(1, array('project' => $project));
                } else {
                    Request::redirectTo('admin/projects');
                }
            }
        }

        View::set('proj', $project);
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
        if ($this->isApi) {
            return API::response(1);
        } else {
            Request::redirectTo('admin/projects');
        }
    }
}
