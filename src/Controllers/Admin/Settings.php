<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
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
use Traq\Models\Setting;

/**
 * Admin Settings controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers
 */
class Settings extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('settings'));
    }

    /**
     * Traq Settings page.
     *
     * @return \Avalon\Http\Response
     */
    public function indexAction()
    {
        return $this->render('admin/settings/index.phtml');
    }

    /**
     * Save settings.
     *
     * @return \Avalon\Http\RedirectResponse
     */
    public function saveAction()
    {
        foreach ($this->request->post('settings', []) as $setting => $value) {
            $setting = Setting::find("setting", $setting);

            if ($setting) {
                $setting->value = $value;
                $setting->save();
            }
        }

        return $this->redirectTo("admin_settings");
    }
}
