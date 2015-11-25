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
use Traq\Models\Type;
use Traq\Traits\Controllers\CRUD;

/**
 * Admin Types controller
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\Admin
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

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('types'));
    }

    protected function modelParams()
    {
        return [
            'name'              => $this->request->post('name'),
            'bullet'            => $this->request->post('bullet'),
            'show_on_changelog' => $this->request->post('show_on_changelog', true),
            'template'          => $this->request->post('template')
        ];
    }
}
