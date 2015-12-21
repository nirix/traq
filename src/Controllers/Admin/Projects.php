<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
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
use Traq\Models\Project;
use Traq\Traits\Controllers\CRUD;

/**
 * Admin Projects controller
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\Admin
 */
class Projects extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\Project';
    protected $viewsDir = 'admin/projects';

    // Singular and plural form
    protected $singular = 'project';
    protected $plural   = 'projects';

    // Redirect route names
    protected $afterCreateRedirect  = 'admin_projects';
    protected $afterSaveRedirect    = 'admin_projects';
    protected $afterDestroyRedirect = 'admin_projects';

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('projects'));
    }

    /**
     * @return array
     */
    protected function modelParams()
    {
        return [
            'name'                   => Request::$post['name'],
            'slug'                   => Request::$post['slug'],
            'codename'               => Request::$post['codename'],
            'info'                   => Request::$post['info'],
            'enable_wiki'            => (bool) Request::$post->get('enable_wiki', false),
            'default_ticket_type_id' => Request::$post['default_ticket_type_id'],
            'default_ticket_sorting' => Request::$post['default_ticket_sorting'],
            'display_order'          => (int) Request::$post->get('display_order', 0)
        ];
    }
}
