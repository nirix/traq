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

namespace Traq\Http\Controllers\Project;

use Illuminate\Http\Request;
use Traq\Http\Controllers\Controller;
use Traq\Http\Requests\StoreProjectRequest;
use Traq\Project;

class SettingsController extends Controller
{
    public function index(Project $project)
    {
        return view('projects/edit', [
            'project' => $project
        ]);
    }

    /**
     * Update settings if validation passes.
     *
     * @param Project $project
     * @param StoreProjectRequest $request
     */
    public function update(Project $project, StoreProjectRequest $request)
    {
        $project->update([
            'name' => $request->get('name'),
            'slug' => $request->get('slug'),
            'description' => $request->get('description'),
            'default_status_id' => $request->get('default_status'),
            'default_priority_id' => $request->get('default_priority'),
            'enable_wiki' => $request->get('enable_wiki') ?? false,
        ]);

        return redirect(route('projects.settings', ['project' => $project]))
            ->with(__('projects.successfully_updated'));
    }
}
