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

use Traq\Controllers\AppController as BaseAppController;

/**
 * Base admin controller.
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 3.0.0
 */
class AppController extends BaseAppController
{
    public function __construct()
    {
        parent::__construct();

        $this->addCrumb($this->translate('admincp'), $this->generateUrl('admincp'));

        // Admins only, kthnxbai
        if (!$this->currentUser || !$this->currentUser->isAdmin()) {
            $this->before('*', function () {
                return $this->show403();
            });
        }
    }
}
