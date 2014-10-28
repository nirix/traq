<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
 * https://github.com/nirix
 * http://traq.io
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

use Traq\Models\User;
use Traq\Models\Ticket;

/**
 * AdminCP Dashboard
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq\Controllers
 */
class Dashboard extends AppController
{
    /**
     * Dashboard index page.
     */
    public function indexAction()
    {
        // Check for update
        $this->checkForUpdate();

        // Get information
        $info = array(
            'users'       => User::select()->rowCount(),
            'latest_user' => User::select()->orderBy('id', 'DESC')->fetch(),
            'projects'    => User::select()->rowCount(),
        );

        // Tickets
        $info['tickets'] = array(
            'open'   => Ticket::select()->where('is_closed = ?', 0)->rowCount(),
            'closed' => Ticket::select()->where('is_closed = ?', 1)->rowCount(),
        );

        $this->set($info);
    }

    /**
     * Check for update
     */
    private function checkForUpdate()
    {
        if ($update = @file_get_contents("http://traq.io/version_check.php?version=" . urlencode(TRAQ_VER) . "&code=" . TRAQ_VER_CODE)) {
            $update = json_decode($update, true);
            $this->set(compact('update'));
        }
    }
}
