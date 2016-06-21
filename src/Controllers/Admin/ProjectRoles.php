<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
 * Copyright (C) 2012-2015 Traq.io
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

namespace Traq\Controllers\Admin;

use Avalon\Http\Request;
use Traq\Models\ProjectRole;
use Traq\Traits\Controllers\CRUD;

/**
 * Admin Types controller
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 4.0.0
 */
class ProjectRoles extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\ProjectRole';
    protected $viewsDir = 'admin/project_roles';

    // Singular and plural form
    protected $singular = 'role';
    protected $plural   = 'roles';

    // Redirect route names
    protected $afterCreateRedirect  = 'admin_project_roles';
    protected $afterSaveRedirect    = 'admin_project_roles';
    protected $afterDestroyRedirect = 'admin_project_roles';

    // Route names
    protected $newRoute = 'admin_new_project_role';
    protected $editRoute = 'admin_edit_project_role';

    public function __construct()
    {
        parent::__construct();
        $this->addCrumb($this->translate('project_roles'), $this->generateUrl('admin_project_roles'));
    }

    protected function getAllRows()
    {
        return ProjectRole::select('project_role.*', 'project.name AS project_name')
            ->leftJoin('project_role', PREFIX . 'projects', 'project', 'project.id = project_role.project_id')
            ->execute()
            ->fetchAll();
    }

    /**
     * @return array
     */
    protected function modelParams()
    {
        return [
            'name'          => Request::$post->get('name'),
            'is_assignable' => Request::$post->get('is_assignable', false),
            'project_id'    => Request::$post->get('project_id')
        ];
    }
}
