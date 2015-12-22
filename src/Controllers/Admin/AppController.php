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

/**
 * AdminCP controller
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 3.0.0
 */
class AppController extends \Traq\Controllers\AppController
{
    protected $layout = 'admin.phtml';

    /**
     * Constructor!
     */
    public function __construct()
    {
        parent::__construct();

        // Set the admin layout.
        $this->title($this->translate('admincp'));

        // Make sure the user is logged in and is an admin.
        $this->before('*', function () {
            if ($this->currentUser and !$this->currentUser['is_admin']) {
                return $this->show403();
            } elseif (!$this->currentUser) {
                return $this->showLogin(Request::$requestUri);
            }
        });
    }
}
