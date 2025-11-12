<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

use Avalon\Http\Request;
use Avalon\Http\Router;
use Avalon\Output\View;
use Traq\Controllers\AppController;
use traq\helpers\API;
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
    public function view(string $slug)
    {
        // Get the page
        $page = $this->project->wiki_pages->where('slug', $slug)->exec();

        // Check if the page exists
        if (!$page->rowCount()) {
            // it doesn't, show the new page form if the user has permission
            // otherwise display the 404 page.
            return $this->user->permission($this->project->id, 'create_wiki_page') ? $this->_newPage($slug) : $this->show404();
        }

        $page = $page->fetch();
        $this->title($page->title);

        return $this->render('wiki/view', ['page' => $page]);
    }

    /**
     * Displays all the wiki pages for the project.
     */
    public function pages()
    {
        // Fetch all the projects wiki pages
        $pages = $this->project->wiki_pages->exec()->fetch_all();

        $this->title(l('pages'));

        return $this->render('wiki/pages', ['pages' => $pages]);
    }

    /**
     * Displays the new wiki page form.
     */
    public function create()
    {
        // Get slug
        $slug = isset(Router::$params['slug']) ? Router::$params['slug'] : '';

        $this->title(l('new'));

        // Fetch the page from the database
        $page = new WikiPage(array('slug' => $slug));

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Update the page information
            $page->set(array(
                'title'      => Request::get('title'),
                'slug'       => Request::get('slug'),
                'project_id' => $this->project->id
            ));

            // Set revision data
            $page->revision->set(array(
                'revision' => 1,
                'user_id'  => $this->user->id,
                'content'  => Request::get('body')
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

                if ($this->isJson) {
                    $this->json(['page' => $page]);
                } else {
                    Request::redirectTo($page->href());
                }
            }
        }

        return $this->render('wiki/new', ['page' => $page]);
    }

    /**
     * Displays the edit wiki page form.
     */
    public function edit(string $slug)
    {
        $this->title(l('edit'));

        // Fetch the page from the database
        $page = $this->project->wiki_pages->where('slug', $slug)->exec()->fetch();

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            // Update the page information
            $page->set(array(
                'title'      => Request::get('title'),
                'slug'       => Request::get('slug'),
                'project_id' => $this->project->id
            ));

            if (Request::get('body') != $page->revision->content) {
                $page->revision = new WikiRevision(array(
                    'wiki_page_id' => $page->id,
                    'revision'     => $page->revision->revision + 1,
                    'content'      => Request::get('body'),
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

                if ($this->isJson) {
                    $this->json(['page' => $page]);
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
    public function delete(string $slug)
    {
        $page = $this->project->wiki_pages->where('slug', $slug)->exec()->fetch();

        // Timeline events
        $createEvent = Timeline::select()->where('action', 'wiki_page_created')->where('owner_id', $page->id)->exec()->fetch()->delete();
        $updateEvents = Timeline::select()->where('action', 'wiki_page_edited')->where('owner_id', $page->id)->exec()->fetchAll();

        foreach ($updateEvents as $event) {
            $event->delete();
        }

        foreach ($page->revisions->exec()->fetchAll() as $revision) {
            $revision->delete();
        }

        // Delete the page
        $this->project->wiki_pages->where('slug', $slug)->exec()->fetch()->delete();

        // Redirect to main page
        if ($this->isJson) {
            return $this->json(['success' => true]);
        } else {
            return Request::redirectTo($this->project->href('wiki'));
        }
    }

    /**
     * Page revisions listing.
     *
     * @param string $slug
     */
    public function revisions(string $slug)
    {
        $page = WikiPage::select()->where('project_id', $this->project->id)->where('slug', $slug)->exec()->fetch();

        $this->title($page->title);
        $this->title(l('revisions'));

        return $this->render('wiki/revisions', ['page' => $page]);
    }

    /**
     * View revision.
     *
     * @param string  $slug
     * @param integer $revision
     */
    public function revision(string $slug, int $revision)
    {
        $page = WikiPage::select()->where('project_id', $this->project->id)->where('slug', $slug)->exec()->fetch();
        $page->revision = $page->revisions->where('revision', $revision)->exec()->fetch();

        $this->title($page->title);
        $this->title(l('revision_x', $revision));

        return $this->render('wiki/view', ['page' => $page]);
    }

    /**
     * Used by the action_view method if the page
     * to display the new page form if the requested
     * page doesn't exist.
     *
     * @param string $slug The slug for the wiki page.
     */
    private function _newPage(string $slug)
    {
        return $this->create($slug);
    }

    /**
     * Used to check the permission for the requested action.
     */
    public function _check_permission()
    {
        $action = (Router::$method == 'new' ? 'create' : Router::$method);

        // Check if the user has permission
        if (!$this->user->permission($this->project->id, "{$action}_wiki_page")) {
            // oh noes! display the no permission page.
            $this->show_no_permission();
            return false;
        }
    }
}
