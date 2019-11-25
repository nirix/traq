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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Traq\Http\Controllers\Controller;
use Traq\Project;
use Traq\WikiPage;
use Traq\WikiRevision;

class WikiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['index', 'show', 'list']
        ]);
    }

    /**
     * Render the wiki page with the slug of 'main'.
     *
     * @param Request $request
     * @param Project $project
     */
    public function index(Request $request, Project $project)
    {
        return $this->renderPage($project, 'main');
    }

    /**
     * Render the page creation form.
     *
     * @param Request $request
     * @param Project $project
     */
    public function create(Request $request, Project $project)
    {
        $this->authorize('create', WikiPage::class);

        $page = new WikiPage();
        $revision = new WikiRevision();

        if ($request->has('slug')) {
            $page->slug = $request->get('slug');
        }

        return view('wiki/create', [
            'project' => $project,
            'page' => $page,
            'revision' => $revision,
        ]);
    }

    /**
     * Store the page and revision if there are no errors.
     *
     * @param Request $request
     * @param Project $project
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('create', WikiPage::class);
        $this->validatePage($request, $project);

        $page = new WikiPage([
            'title' => $request->get('title'),
            'slug' => $request->get('slug'),
            'project_id' => $project->id,
        ]);

        $revision = new WikiRevision([
            'content' => $request->get('content'),
            'user_id' => auth()->id(),
        ]);

        $page->save();
        $revision->wiki_page_id = $page->id;
        $revision->save();

        return redirect(route('wiki.show', ['project' => $project, 'wiki' => $page]));
    }

    /**
     * Render the requested wiki page.
     *
     * @param Request $request
     * @param Project $project
     * @param string  $slug    Wiki page slug
     */
    public function show(Request $request, Project $project, string $slug)
    {
        return $this->renderPage($project, $slug);
    }

    public function revisions(Project $project, string $slug)
    {
        $page = $project->wikiPages()
            ->where('slug', '=', $slug)
            ->first();

        if (!$page) {

        }

        return view('wiki/revisions', [
            'project' => $project,
            'page' => $page,
        ]);
    }

    /**
     * Edit page form.
     *
     * @param Request $request
     * @param Project $project
     * @param string  $slug
     */
    public function edit(Request $request, Project $project, string $slug)
    {
        $page = $project->wikiPages()
            ->where('slug', '=', $slug)
            ->first();

        $this->authorize('update', $page);

        return view('wiki/edit', [
            'project' => $project,
            'revision' => $page->latestRevision(),
            'page' => $page,
        ]);
    }

    /**
     * Save page and create new revision if validation passes.
     *
     * @param Request $request
     * @param Project $project
     * @param string  $slug
     */
    public function update(Request $request, Project $project, string $slug)
    {

        $page = $project->wikiPages()
            ->where('slug', '=', $slug)
            ->first();

        $this->authorize('update', $page);
        $this->validatePage($request, $project, $page);

        $revision = $page->latestRevision();

        $page->update([
            'title' => $request->get('title'),
            'slug' => $request->get('slug'),
        ]);

        $page->revisions()->create([
            'content' => $request->get('content'),
            'user_id' => auth()->id(),
            'revision' => ($revision->revision + 1),
        ]);

        $page->save();

        return redirect(route('wiki.show', ['project' => $project, 'wiki' => $page]));
    }

    public function pages(Project $project)
    {
        $pages = $project->wikiPages();

        return view('wiki/pages', [
            'project' => $project,
            'pages' => $pages,
        ]);
    }

    /**
     * Fetch and render the wiki page if it exists, else redirect to the creation form and fill in the slug.
     *
     * @param Project $project
     * @param string  $slug    Wiki page slug
     */
    private function renderPage(Project $project, string $slug)
    {
        $page = $project->wikiPages()
            ->where('slug', '=', $slug)
            ->first();

        if (!$page) {
            return redirect(route('wiki.create', ['project' => $project, 'slug' => $slug]));
        }

        return view('wiki/show', [
            'project' => $project,
            'page' => $page,
        ]);
    }

    /**
     * Validate the store/update page request. We build the validation here and not in a request object
     * so we can more easily restrict the validation down to the current project and page.
     *
     * @param Request  $request
     * @param Project  $project
     * @param WikiPage $page
     */
    protected function validatePage(
        Request $request,
        Project $project,
        WikiPage $page = null
    ):void {
        // Validate the uniqueness of the slug within the pages project.
        $slugRule = Rule::unique('wiki_pages')->where(function ($query) use($project) {
            return $query->where('project_id', $project->id);
        });

        // Ignore the page itself.
        if ($page) {
            $slugRule->ignore($page->id);
        }

        Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'slug' => $slugRule,
                'content' => 'required',
            ]
        )->validate();
    }
}
