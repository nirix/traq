<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

namespace traq\controllers\admin;

use avalon\output\View;

use traq\models\User;
use traq\models\Ticket;

/**
 * AdminCP Dashboard
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Dashboard extends AppController
{
    /**
     * Dashboard index page.
     */
    public function action_index()
    {
        // Check for update
        $this->check_for_update();

        // Get information
        $info = array(
            'users'       => User::select()->exec()->row_count(),
            'latest_user' => User::select()->order_by('id', 'DESC')->exec()->fetch(),
            'projects'    => User::select()->exec()->row_count(),
        );

        // Tickets
        $info['tickets'] = array(
            'open'   => Ticket::select()->where('is_closed', 0)->exec()->row_count(),
            'closed' => Ticket::select()->where('is_closed', 1)->exec()->row_count(),
        );

        View::set($info);
    }

    /**
     * Check for update
     */
    private function check_for_update()
    {
        if ($update = @file_get_contents("http://traq.io/version_check.php?version=" . urlencode(TRAQ_VER) . "&code=" . TRAQ_VER_CODE)) {
            $update = json_decode($update, true);
            View::set(compact('update'));
        }
    }
}
