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

namespace traq\controllers;

use avalon\http\Request;
use avalon\http\Router;
use avalon\output\View;

use traq\models\WikiPage;
use traq\models\WikiRevision;
use traq\models\Timeline;

/**
 * Wiki controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Wiki extends AppController
{
    // Before filters
    public $before = array(
        'new'    => array('_check_permission'),
        'edit'   => array('_check_permission'),
        'delete' => array('_check_permission')
    );

    /**
     * Wiki controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Set the title
        $this->title(l('wiki'));
    }

    /**
     * Displays the requested wiki page.
     */
    public function action_view()
    {
        // Get slug
        $slug = \avalon\http\Router::$params['slug'];

        // Get the page
        $page = $this->project->wiki_pages->where('slug', $slug)->exec();

        // Check if the page exists
        if (!$page->row_count()) {
            // it doesnt, show the new page form if the user has permission
            // otherwise display the 404 page.
            return current_user()->permission($this->project->id, 'create_wiki_page') ? $this->_new_page($slug) : $this->show_404();
        }

        View::set('page', $page->fetch());
    }

    /**
     * Displays all the wiki pages for the project.
     */
    public function action_pages()
    {
        // Fetch all the projects wiki pages
        $pages = $this->project->wiki_pages->exec()->fetch_all();

        $this->title(l('pages'));
        View::set('pages', $pages);
    }

    /**
     * Displays the new wiki page form.
     */
    public function action_new()
    {
        // Get slug
        $slug = isset(Router::$params['slug']) ? Router::$params['slug'] : '';

        $this->title(l('new'));

        // Fetch the page from the database
        $page = new WikiPage(array('slug' => $slug));

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Update the page information
            $page->set(array(
                'title'      => Request::post('title'),
                'slug'       => Request::post('slug'),
                'project_id' => $this->project->id
            ));

            // Set revision data
            $page->revision->set(array(
                'revision' => 1,
                'user_id'  => $this->user->id,
                'content'  => Request::post('body')
            ));

            // Save and redirect
            if ($page->save()) {
                // Save revision
                $page->revision->wiki_page_id = $page->id;
                $page->revision->save();

                // Set pages revision ID
                $page->revision_id = $page->revision->id;
                $page->_is_new(false);
                $page->save();

                // Insert timeline event
                $timeline = new Timeline(array(
                    'project_id' => $this->project->id,
                    'owner_id'   => $page->id,
                    'action'     => 'wiki_page_created',
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
    public function action_revisions($slug)
    {
        $page = WikiPage::select()->where('project_id', $this->project->id)->where('slug', $slug)->exec()->fetch();
        View::set(compact('page'));
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
     * Used by the action_view method if the page
     * to display the new page form if the requested
     * page doesn't exist.
     *
     * @param string $slug The slug for the wiki page.
     */
    private function _new_page($slug)
    {
        $this->render['view'] = 'wiki/new';
        $this->action_new($slug);
    }

    /**
     * Used to check the permission for the requested action.
     */
    public function _check_permission()
    {
        $action = (Router::$method == 'new' ? 'create' : Router::$method);

        // Check if the user has permission
        if (!current_user()->permission($this->project->id, "{$action}_wiki_page")) {
            // oh noes! display the no permission page.
            $this->show_no_permission();
            return false;
        }
    }
}
