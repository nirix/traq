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

use Traq\Models\User;
use Traq\Models\Ticket;
use Traq\Models\Setting;

/**
 * Dashboard controller.
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 3.0.0
 */
class Dashboard extends AppController
{
    /**
     * Dashboard index page.
     */
    public function indexAction()
    {
        // Check for update
        $lastUpdateCheck = Setting::find('setting', 'last_update_check');
        if ($lastUpdateCheck->value <= (time() - 86400)) {
            $this->checkForUpdate();
            $lastUpdateCheck->value = time();
            $lastUpdateCheck->save();
        }

        // Get information
        $info = [
            'users'       => User::select('id')->rowCount(),
            'newestUser'  => User::select('id', 'name')->orderBy('id', 'DESC')->execute()->fetch(),
            'projects'    => User::select('id')->rowCount()
        ];

        // Issues
        $info['tickets'] = [
            'open'   => Ticket::select('id')->where('is_closed = ?')->setParameter(0, 0)->rowCount(),
            'closed' => Ticket::select('id')->where('is_closed = ?')->setParameter(0, 1)->rowCount()
        ];

        return $this->render('admin/dashboard/index.phtml', $info);
    }

    /**
     * Check for update
     */
    private function checkForUpdate()
    {
        $url = sprintf(
            "https://traq.io/version_check.php?version=%s&code=%s",
            urlencode(\Traq\VERSION),
            \Traq\VERSION_ID
        );

        if ($update = @file_get_contents($url)) {
            $update = json_decode($update, true);
            $this->set(compact('update'));
        }
    }
}
