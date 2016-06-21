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

use Avalon\Http\Request;
use Traq\Language;
use Traq\Models\Setting;
use Traq\Themes;

/**
 * Admin settings controller.
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 3.0.0
 */
class Settings extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->addCrumb($this->translate('settings'), $this->generateUrl('admin_settings'));

        // Ticket history sorting select options
        $this->set('historySortingSelectOptions', [
            ['label' => $this->translate("oldest_first"), 'value' => "oldest_first"],
            ['label' => $this->translate("newest_first"), 'value' => "newest_first"]
        ]);

        Themes::index();
        $this->set('themes', Themes::selectOptions());
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
        foreach (Request::$post->get('settings', [], false) as $setting => $value) {
            $setting = Setting::find("setting", $setting);

            if ($setting) {
                $setting->value = $value;
                $setting->save();
            }
        }

        return $this->redirectTo("admin_settings");
    }
}
