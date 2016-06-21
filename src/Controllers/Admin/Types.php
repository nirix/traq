<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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
use Traq\Models\Type;
use Traq\Traits\Controllers\CRUD;

/**
 * Admin Types controller
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 3.0.0
 */
class Types extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\Type';
    protected $viewsDir = 'admin/types';

    // Singular and plural form
    protected $singular = 'type';
    protected $plural   = 'types';

    // Redirect route names
    protected $afterCreateRedirect  = 'admin_types';
    protected $afterSaveRedirect    = 'admin_types';
    protected $afterDestroyRedirect = 'admin_types';

    // Route names
    protected $newRoute = 'admin_new_type';
    protected $editRoute = 'admin_edit_type';

    public function __construct()
    {
        parent::__construct();
        $this->addCrumb($this->translate('types'), $this->generateUrl('admin_types'));
    }

    protected function modelParams()
    {
        return [
            'name'              => Request::$post->get('name'),
            'bullet'            => Request::$post->get('bullet'),
            'show_on_changelog' => Request::$post->get('show_on_changelog', false),
            'template'          => Request::$post->get('template')
        ];
    }
}
