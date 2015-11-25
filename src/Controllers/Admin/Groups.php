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
use Traq\Models\Group;
use Traq\Traits\Controllers\CRUD;

/**
 * Admin Groups controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\Admin
 */
class Groups extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\Group';
    protected $viewsDir = 'admin/groups';

    // Singular and plural form
    protected $singular = 'group';
    protected $plural   = 'groups';

    // Redirect route names
    protected $afterCreateRedirect  = 'admin_groups';
    protected $afterSaveRedirect    = 'admin_groups';
    protected $afterDestroyRedirect = 'admin_groups';

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('groups'));
    }

    /**
     * @return array
     */
    protected function modelParams()
    {
        return [
            'name'     => Request::post('name'),
            'is_admin' => Request::post('is_admin', false)
        ];
    }
}
