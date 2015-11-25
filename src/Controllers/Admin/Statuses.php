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
use Traq\Models\Status;
use Traq\Traits\Controllers\CRUD;

/**
 * Admin Statuses controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\Admin
 */
class Statuses extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\Status';
    protected $viewsDir = 'admin/statuses';

    // Singular and plural form
    protected $singular = 'status';
    protected $plural   = 'statuses';

    // Redirect route names
    protected $afterCreateRedirect  = 'admin_statuses';
    protected $afterSaveRedirect    = 'admin_statuses';
    protected $afterDestroyRedirect = 'admin_statuses';

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('statuses'));

        $this->set('typeSelectOptions', [
            ['label' => $this->translate('status.type.1'), 'value' => 1],
            ['label' => $this->translate('status.type.2'), 'value' => 2],
            ['label' => $this->translate('status.type.0'), 'value' => 0]
        ]);
    }

    /**
     * @return array
     */
    protected function modelParams()
    {
        return [
            'name'              => Request::post('name'),
            'status'            => Request::post('type'),
            'show_on_changelog' => Request::post('show_on_changelog', false)
        ];
    }
}
