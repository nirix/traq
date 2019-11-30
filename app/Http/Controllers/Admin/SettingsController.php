<?php
/*!
 * Traq
 *
 * Copyright (C) 2009-2019 Jack P.
 * Copyright (C) 2012-2019 Traq.io
 * https://github.com/nirix
 * https://traq.io
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Traq\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Traq\Http\Controllers\Controller;
use Traq\Http\Requests\UpdateSettingsRequest;
use Traq\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin/settings/index');
    }

    /**
     * Save settings if validation passes.
     *
     * @param UpdateSettingsRequest $request
     */
    public function update(UpdateSettingsRequest $request)
    {
        $settings = [
            'traq_name'
        ];

        foreach ($settings as $settingName) {
            $setting = Setting::where('name', '=', $settingName)->firstOrCreate([
                'name' => 'traq_name'
            ]);
            $setting->value = $request->get($settingName);
            $setting->save();
        }

        return redirect(route('admin.settings'))
            ->with('success', Lang::get('admin.settings_updated'));
    }
}
