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
use Traq\Http\Controllers\Controller;
use Traq\Http\Requests\StoreUserGroupRequest;
use Traq\UserGroup;

class UserGroupController extends Controller
{
    public function index()
    {
        $this->authorize('manage', UserGroup::class);

        $userGroups = UserGroup::all();

        return view('admin/usergroups/index', [
            'userGroups' => $userGroups
        ]);
    }

    public function create()
    {
        $this->authorize('create', UserGroup::class);

        $userGroup = new UserGroup();

        return view('admin/usergroups/create', [
            'userGroup' => $userGroup
        ]);
    }

    /**
     * Create group if validation passes.
     *
     * @param StoreUserGroupRequest $request
     */
    public function store(StoreUserGroupRequest $request)
    {
        $userGroup = new UserGroup([
            'name' => $request->get('name'),
        ]);

        $userGroup->save();

        return redirect(route('admin.user-groups.index'))
            ->with('success', __('admin.usergroups.created_successfully'));
    }

    public function edit(UserGroup $userGroup)
    {
        $this->authorize('update', $userGroup);

        return view('admin/usergroups/edit', [
            'userGroup' => $userGroup
        ]);
    }

    /**
     * Save group if validation passes.
     *
     * @param StoreUserGroupRequest $request
     */
    public function update(
        UserGroup $userGroup,
        StoreUserGroupRequest $request
    ) {
        $userGroup->update([
            'name' => $request->get('name')
        ]);

        return redirect(route('admin.user-groups.index'))
            ->with('success', __('admin.usergroups.updated_successfully'));
    }

    /**
     * Delete group.
     *
     * @param UserGroup $userGroup
     */
    public function destroy(UserGroup $userGroup)
    {
        $this->authorize('delete', UserGroup::class);
    }
}
