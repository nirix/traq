<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

use Avalon\Output\View;
use Traq\Models\User;
use Traq\Models\Ticket;

/**
 * AdminCP Dashboard
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class DashboardController extends AppController
{
    /**
     * Dashboard index page.
     */
    public function index()
    {
        // Check for update
        $this->checkForUpdate();

        // Get information
        $info = [
            'users'       => User::select()->exec()->row_count(),
            'latest_user' => User::select()->order_by('id', 'DESC')->exec()->fetch(),
            'projects'    => User::select()->exec()->row_count(),
        ];

        // Tickets
        $info['tickets'] = [
            'open'   => Ticket::select()->where('is_closed', 0)->exec()->row_count(),
            'closed' => Ticket::select()->where('is_closed', 1)->exec()->row_count(),
        ];

        return $this->render('admin/dashboard/index.phtml', $info);
    }

    /**
     * Check for update
     */
    private function checkForUpdate()
    {
        try {
            $update = file_get_contents("https://traq.io/version_check.php?version=" . urlencode(TRAQ_VER) . "&code=" . TRAQ_VER_CODE);
            $update = json_decode($update, true);
            View::set(compact('update'));
        } catch (\Exception $e) {
            // Unable to check for update
        }
    }
}
