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

use Traq\User;
use Illuminate\Support\Facades\Hash;
use Traq\Http\Requests\StoreUserRequest;

class AccountController extends Controller
{
    public function index()
    {
        return view('installer.account.form', [
            'step' => 'Admin User',
        ]);
    }

    public function create(StoreUserRequest $request)
    {
        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'group_id' => 1,
        ]);

        $anon = new User([
            'name' => 'Anonymous',
            'email' => 'anon@'.$request->getHttpHost(),
            'password' => \md5(\time().\microtime().\rand(0, 10000)),
            'group_id' => 3,
        ]);

        $user->save();
        $anon->save();

        return redirect(route('installer_complete'));
    }
}
