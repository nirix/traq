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
use Traq\Models\Severity;
use Traq\Traits\Controllers\CRUD;

/**
 * Severities controller
 *
 * @author Jack P.
 * @since 3.0.0
* @package Traq\Controllers\Admin
 */
class Severities extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\Severity';
    protected $viewsDir = 'admin/severities';

    // Singular and plural form
    protected $singular = 'severity';
    protected $plural   = 'severities';

    // Redirect route names
    protected $afterCreateRedirect  = 'admin_severities';
    protected $afterSaveRedirect    = 'admin_severities';
    protected $afterDestroyRedirect = 'admin_severities';

    // Route names
    protected $newRoute = 'admin_new_severity';
    protected $editRoute = 'admin_edit_severity';

    public function __construct()
    {
        parent::__construct();
        $this->addCrumb($this->translate('severities'), $this->generateUrl('admin_severities'));
    }

    /**
     * @return array
     */
    protected function modelParams()
    {
        return [
            'name'  => Request::$post->get('name'),
            'level' => Request::$post->get('level')
        ];
    }
}
