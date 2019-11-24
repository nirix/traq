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

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Traq\Http\Controllers\Controller;
use Traq\Http\Requests\StoreMilestoneRequest;
use Traq\Milestone;
use Traq\Project;

class MilestoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['index', 'show']
        ]);
    }

    /**
     * List milestones based on current view.
     *
     * @param Project $project
     * @param string  $filter
     */
    public function index(Project $project, string $filter = 'active')
    {
        if ($filter === 'active') {
            $milestones = $project->activeMilestones;
        } elseif ($filter === 'completed') {
            $milestones = $project->completedMilestones;
        } else {
            $milestones = $project->milestones;
        }

        return view('milestones/index', [
            'project' => $project,
            'milestones' => $milestones,
            'filter' => $filter,
        ]);
    }

    /**
     * Show milestone.
     *
     * @param Project   $project
     * @param Milestone $milestone
     */
    public function show(Project $project, Milestone $milestone)
    {
        return view('milestones/show', [
            'project' => $project,
            'milestone' => $milestone
        ]);
    }

    /**
     * New milestone form.
     *
     * @param Project $project
     */
    public function create(Project $project)
    {
        $milestone = new Milestone();

        return view('milestones/create', [
            'project' => $project,
            'milestone' => $milestone,
        ]);
    }

    /**
     * Create milestone if validation passes.
     *
     * @param Request $request
     * @param Project $project
     */
    public function store(Request $request, Project $project)
    {
        $this->validateMilestone($request, $project);

        $milestone = new Milestone([
            'name' => $request->get('name'),
            'codename' => $request->get('codename'),
            'slug' => $request->get('slug'),
            'description' => $request->get('description'),
            'status' => $request->get('status'),
            'due_at' => $request->get('due_at'),
            'display_order' => $request->get('display_order'),
            'project_id' => $project->id,
        ]);

        if ($milestone->status === Milestone::STATUS_COMPLETED && $milestone->closed_at === null) {
            $milestone->closed_at = now();
        }

        $milestone->save();

        return redirect(
            route('milestones.index', ['project' => $project])
        )->with('success', __('milestones.created_successfully'));
    }

    /**
     * Edit milestone form.
     *
     * @param Project $project
     * @param Milestone $milestone
     */
    public function edit(Project $project, Milestone $milestone)
    {
        return view('milestones/edit', [
            'project' => $project,
            'milestone' => $milestone,
        ]);
    }

    /**
     * Save milestone if validation passes.
     *
     * @param Request   $request
     * @param Project   $project
     * @param Milestone $milestone
     */
    public function update(Request $request, Project $project, Milestone $milestone)
    {
        $milestone->name = $request->get('name');
        $milestone->codename = $request->get('codename');
        $milestone->slug = $request->get('slug');
        $milestone->description = $request->get('description');
        $milestone->status = $request->get('status');
        $milestone->due_at = $request->get('due_at');
        $milestone->display_order = $request->get('display_order');

        // If the milestone is marked as complete, set the completed at if it wasn't already set.
        if ($milestone->status === Milestone::STATUS_COMPLETED && $milestone->closed_at === null) {
            $milestone->closed_at = now();
        } elseif($milestone->status === Milestone::STATUS_ACTIVE) {
            $milestone->closed_at = null;
        }

        $this->validateMilestone($request, $project, $milestone);

        $milestone->save();

        return redirect(
            route('milestones.show', ['project' => $project, 'milestone' => $milestone])
        )->with('success', __('milestones.updated_successfully'));
    }

    /**
     * Show project changelog.
     *
     * @param Request $request
     * @param Project $project
     */
    public function changelog(Request $request, Project $project)
    {
        $milestones = $project->completedMilestones()
            ->orderBy('display_order', 'DESC')
            ->with('tickets')
            ->get();

        return view('milestones/changelog', [
            'project' => $project,
            'milestones' => $milestones,
        ]);
    }

    /**
     * Validate the create/update milestone request.
     *
     * @param Request   $request
     * @param Project   $project
     * @param Milestone $milestone
     *
     * @return void
     */
    protected function validateMilestone(
        Request $request,
        Project $project,
        Milestone $milestone = null
    ):void {
        // Validate the uniqueness of the slug within the milestones project.
        $slugRule = Rule::unique('milestones')->where(function ($query) use($project) {
            return $query->where('project_id', $project->id);
        });

        // Ignore the milestone itself.
        if ($milestone) {
            $slugRule->ignore($milestone->id);
        }

        Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'slug' => $slugRule,
                'display_order' => 'required',
            ]
        )->validate();
    }
}
