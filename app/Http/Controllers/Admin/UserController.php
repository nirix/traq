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
use Illuminate\Support\Facades\Hash;
use Traq\Http\Controllers\Controller;
use Traq\Http\Requests\StoreUserRequest;
use Traq\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select()->with('group')->orderBy('name')->get();

        return view('admin/users/index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $user = new User();

        return view('admin/users/create', [
            'model' => $user
        ]);
    }

    /**
     * Create user if validation passes.
     *
     * @param StoreUserRequest $request
     */
    public function store(StoreUserRequest $request)
    {
        User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        return redirect(route('admin.users.index'))
            ->with('success', 'users.created_successfully');
    }
}
