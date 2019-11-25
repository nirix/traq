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

namespace Traq\Http\Controllers\Installer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index()
    {
        $result = $this->checkConnection();

        if ($result) {
            return $this->runMigrations();
        } else {
            return view('installer.database.check', [
                'step' => 'Database Installation',
                'connection' => false,
            ]);
        }
    }

    private function checkConnection(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function runMigrations()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            return redirect(route('installer_user'));
        } catch (\Exception $e) {
            return view('installer.database.install', [
                'step' => 'Database Installation',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
