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
        $projects = Project::all();

        return $this->respondTo(function($format) use ($projects) {
            if ($format == 'html') {
                return $this->render('admin/projects/index.phtml', [
                    'projects' => $projects
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($projects);
            }
        });
    }

    /**
     * Create a new project page.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        if ($this->isOverlay) {
            return $this->render('admin/projects/new.overlay.phtml', [
                'project' => new Project
            ]);
        } else {
            return $this->render('admin/projects/new.phtml', [
                'project' => new Project
            ]);
        }
    }

    /**
     * Create new project.
     */
    public function createAction()
    {
        $this->title($this->translate('new'));

        $project = new Project($this->projectParams());

        if ($project->save()) {
            return $this->redirectTo('/admin/projects');
        }

        return $this->respondTo(function($format) use($project) {
            if ($format == 'html') {
                return $this->render('admin/projects/new.phtml', [
                    'project' => $project
                ]);
            }
        });
    }

    /**
     * Edit project page.
     */
    public function editAction($project_id)
    {
        $this->title($this->translate('edit'));

        $project = Project::find($project_id);

        if ($this->isOverlay) {
            return $this->render('admin/projects/edit.overlay.phtml', [
                'project' => $project
            ]);
        } else {
            return $this->respondTo(function($format) use ($project) {
                if ($format == 'html') {
                    return $this->render('admin/projects/edit.phtml', [
                        'project' => $project
                    ]);
                } elseif ($format == 'json') {
                    return $this->jsonResponse($project->toArray());
                }
            });
        }
    }

    /**
     * Save project.
     */
    public function saveAction($project_id)
    {
        $this->title($this->translate('edit'));

        $project = Project::find($project_id);
        $project->set($this->projectParams());

        if ($project->save()) {
            return $this->redirectTo('/admin/projects');
        } else {
            return $this->render('admin/projects/edit.phtml', [
                'project' => $project
            ]);
        }
    }

    /**
     * Delete a project.
     *
     * @param integer $id Project ID.
     */
    public function deleteAction($project_id)
    {
        $project = Project::find($project_id);
        $project->delete();

        // Is this an API request?
        if ($this->isApi) {
            return API::response(200);
        } else {
            return Request::redirectTo('admin/projects');
        }
    }

    /**
     * @return array
     */
    protected function projectParams()
    {
        return [
            'name'                   => Request::$post['name'],
            'slug'                   => Request::$post['slug'],
            'codename'               => Request::$post['codename'],
            'info'                   => Request::$post['info'],
            'enable_wiki'            => (bool) Request::post('enable_wiki', false),
            'default_ticket_type_id' => Request::$post['default_ticket_type_id'],
            'default_ticket_sorting' => Request::$post['default_ticket_sorting'],
            'display_order'          => (int) Request::post('display_order', 0)
        ];
    }
}
