<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
 * https://github.com/nirix
 * http://traq.io
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

use Radium\Http\Request;
use Radium\Http\Response;

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

        $this->before(['new', 'edit', 'delete'], 'checkPermission');
    }

    /**
     * Displays the requested wiki page.
     */
    public function showAction($slug)
    {
        // Get the page
        $page = $this->project->wikiPages()->where('slug = ?', $slug)->fetch();

        // Check if the page exists
        if (!$page) {
            // it doesnt, show the new page form if the user has permission
            // otherwise display the 404 page.
            return current_user()->permission($this->project->id, 'create_wiki_page') ? $this->_newPage($slug) : $this->show404();
        }

        return $this->respondTo(function($format) use($page) {
            if ($format == 'html') {
                return $this->render('wiki/show.phtml', ['page' => $page]);
            } elseif ($format == 'json') {
                return API::response(200, $page->__toArray());
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
                'user_id'      => $this->user->id,
                'wiki_page_id' => $page->id
            ]);
            $page->revision()->save();

            $page->revision_id = $page->revision()->id;
            $page->save();

            $page->revision()->save();
            $this->redirectTo($page->href());
        } else {
            return $this->render('wiki/new.phtml', [
                'page' => $page
            ]);
        }
    }

    /**
     * Displays the edit wiki page form.
     */
    public function action_edit()
    {
        // Get slug
        $slug = \avalon\http\Router::$params['slug'];

        $this->title(l('edit'));

        // Fetch the page from the database
        $page = $this->project->wiki_pages->where('slug', $slug)->exec()->fetch();

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Update the page information
            $page->set(array(
                'title'      => Request::post('title'),
                'slug'       => Request::post('slug'),
                'project_id' => $this->project->id
            ));

            if (Request::post('body') != $page->revision->content) {
                $page->revision = new WikiRevision(array(
                    'wiki_page_id' => $page->id,
                    'revision'     => $page->revision->revision + 1,
                    'content'      => Request::post('body'),
                    'user_id'      => $this->user->id
                ));
            }

            // Save and redirect
            if ($page->save()) {
                // Update revision
                $page->revision->save();
                $page->revision_id = $page->revision->id;
                $page->save();

                // Insert timeline event
                $timeline = new Timeline(array(
                    'project_id' => $this->project->id,
                    'owner_id'   => $page->id,
                    'action'     => 'wiki_page_edited',
                    'user_id'    => $this->user->id
                ));
                $timeline->save();

                if ($this->is_api) {
                    return \API::response(1, array('page' => $page));
                } else {
                    Request::redirectTo($page->href());
                }
            }
        }

        View::set('page', $page);
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
        // $page = WikiPage::select()->where('project_id = ?', $this->project->id)->where('slug', $slug)->exec()->fetch();
        $page = $this->project->wikiPages()->where('slug = ?', $slug)->fetch();
        $this->set(compact('page'));
    }

    /**
     * View revision.
     *
     * @param string  $slug
     * @param integer $revision
     */
    public function action_revision($slug, $revision)
    {
        $page = WikiPage::select()->where('project_id', $this->project->id)->where('slug', $slug)->exec()->fetch();
        $page->revision = $page->revisions->where('revision', $revision)->exec()->fetch();

        View::set(compact('page'));

        $this->render['view'] = 'wiki/view';
    }

    /**
     * Used by the action_view method if the page to display the new page form
     * if the requested page doesn't exist.
     *
     * @param string $slug The slug for the wiki page.
     *
     * @return Response
     */
    private function _newPage($slug)
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
            'user_id'    => $this->user->id
        ];
    }

    /**
     * Used to check the permission for the requested action.
     */
    public function checkPermission()
    {
        $action = ($this->route['method'] == 'new' ? 'create' : $this->route['method']);

        // Check if the user has permission
        if (!$this->user->permission($this->project->id, "{$action}_wiki_page")) {
            // oh noes! display the no permission page.
            return $this->showNoPermission();
        }
    }
}
