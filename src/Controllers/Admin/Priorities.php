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
use Traq\Models\Priority;
use Traq\Traits\Controllers\CRUD;

/**
 * Admin Priorities controller.
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 3.0.0
 */
class Priorities extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\Priority';
    protected $viewsDir = 'admin/priorities';

    // Singular and plural form
    protected $singular = 'priority';
    protected $plural   = 'priorities';

    // Redirect route names
    protected $afterCreateRedirect  = 'admin_priorities';
    protected $afterSaveRedirect    = 'admin_priorities';
    protected $afterDestroyRedirect = 'admin_priorities';

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('priorities'));
    }

    /**
     * @return array
     */
    protected function modelParams()
    {
        return [
            'name' => Request::$post->get('name')
        ];
    }
}
