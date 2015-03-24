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

namespace Traq\Controllers;

use Avalon\Http\Request;
use Avalon\Http\Response;
use Traq\Models\WikiPage;
use Traq\Models\WikiRevision;
use Traq\Models\Timeline;

/**
 * Wiki controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq\Controllers
 */
class Wiki extends AppController
{
    /**
     * Wiki controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Set the title
        $this->title($this->translate('wiki'));

        $this->before(
            ['new', 'create', 'edit', 'save', 'delete'],
            'checkPermission'
        );

        $this->before(
            ['show', 'revisions', 'revision', 'edit', 'save', 'delete'],
            'getPage'
        );
    }

    /**
     * Displays the requested wiki page.
     */
    public function showAction($slug)
    {
        return $this->respondTo(function($format){
            if ($format == 'html') {
                return $this->render('wiki/show.phtml');
            } elseif ($format == 'json') {
                return $this->jsonResponse($this->page->toArray());
            }
        });
    }

    /**
     * Displays all the wiki pages for the project.
     */
    public function pagesAction()
    {
        // Fetch all the projects wiki pages
        $pages = $this->project->wikiPages()->fetchAll();

        $this->title($this->translate('pages'));

        return $this->respondTo(function($format) use ($pages) {
            if ($format == 'html') {
                return $this->render('wiki/pages.phtml', ['pages' => $pages]);
            } elseif ($format == 'json') {
                return API::response(200, $pages);
            }
        });
    }

    /**
     * Displays the new wiki page form.
     */
    public function newAction($slug = null)
    {
        $this->title($this->translate('new'));

        $page = new WikiPage([
            'title' => $slug,
            'slug'  => $slug
        ]);

        return $this->render('wiki/new.phtml', [
            'page' => $page
        ]);
    }

    /**
     * Create wiki page.
     */
    public function createAction()
    {
        $page = new WikiPage($this->pageParams());

        if ($page->save()) {
            $page->revision()->set([
                'user_id'      => $this->currentUser->id,
                'wiki_page_id' => $page->id
            ]);
            $page->revision()->save();

            $page->revision_id = $page->revision()->id;
            $page->save();

            $page->revision()->save();
            return $this->redirect($page->href());
        } else {
            return $this->render('wiki/new.phtml', [
                'page' => $page
            ]);
        }
    }

    /**
     * Displays the edit wiki page form.
     */
    public function editAction($slug)
    {
        $this->title($this->translate('edit'));
        return $this->render('wiki/edit.phtml');
    }

    /**
     * Save wiki page.
     */
    public function saveAction($slug)
    {
        $this->page->set($this->pageParams());

        if (Request::post('content') != $this->page->revision()->content) {
            $revision = new WikiRevision(array(
                'wiki_page_id' => $this->page->id,
                'revision'     => $this->page->revision()->revision + 1,
                'content'      => Request::post('content'),
                'user_id'      => $this->currentUser->id
            ));
        }

        if ($this->page->save()) {
            if (isset($revision)) {
                $revision->save();
                $this->page->revision_id = $revision->id;
            }

            $this->page->save();

            return $this->redirect($this->page->href());
        } else {
            return $this->render('wiki/edit.phtml');
        }
    }

    /**
     * Deletes the specified wiki page.
     */
    public function action_delete($slug)
    {
        // Get slug
        $slug = \avalon\http\Router::$params['slug'];

        // Delete the page
        $this->project->wiki_pages->where('slug', $slug)->exec()->fetch()->delete();

        // Redirect to main page
        if ($this->is_api) {
            return \API::response(1);
        } else {
            Request::redirectTo($this->project->href('wiki'));
        }
    }

    /**
     * Page revisions listing.
     *
     * @param string $slug
     */
    public function revisionsAction($slug)
    {
        $this->title($this->translate('revisions'));
        return $this->render('wiki/revisions.phtml');
    }

    /**
     * View revision.
     *
     * @param string  $slug
     * @param integer $revision
     */
    public function revisionAction($slug, $revision)
    {
        $revision = $this->page->revisions()->where('revision = ?', $revision)->fetch();

        if (!$revision) {
            return $this->show404();
        }

        $this->title($this->translate('revision_x', [$revision->revision]));
        $this->page->setRevision($revision);

        return $this->render('wiki/show.phtml');
    }

    /**
     * Used by the action_view method if the page to display the new page form
     * if the requested page doesn't exist.
     *
     * @param string $slug The slug for the wiki page.
     *
     * @return Response
     */
    protected function newPage($slug)
    {
        return $this->newAction($slug);
    }

    /**
     * @return array
     */
    public function pageParams()
    {
        return [
            'title'      => Request::post('title'),
            'slug'       => Request::post('slug'),
            'content'    => Request::post('content'),
            'project_id' => $this->project->id,
            'user_id'    => $this->currentUser->id
        ];
    }

    /**
     * Gets the current wiki page.
     */
    public function getPage()
    {
        $this->page = $this->project->wikiPages()->where('slug = ?', $this->route->params['slug'])->fetch();

        if (
            !$this->page
            && $this->route->action == 'show'
            && $this->currentUser->permission($this->project->id, 'create_wiki_page')
        ) {
            return $this->newPage($this->route->params['slug']);
        } elseif (!$this->page) {
            return $this->show404();
        }

        $this->title($this->translate($this->page->title));

        $this->set('page', $this->page);
    }

    /**
     * Used to check the permission for the requested action.
     */
    public function checkPermission()
    {
        $action = ($this->route->action == 'new' ? 'create' : $this->route->action);

        // Check if the user has permission
        if (!$this->currentUser->permission($this->project->id, "{$action}_wiki_page")) {
            // oh noes! display the no permission page.
            return $this->showNoPermission();
        }
    }
}
