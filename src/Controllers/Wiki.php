<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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
use Traq\Models\User;
use Traq\Models\WikiPage;
use Traq\Models\WikiRevision;

/**
 * Wiki controller.
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 3.0.0
 */
class Wiki extends AppController
{
    /**
     * @var WikiPage
     */
    protected $page;

    public function __construct()
    {
        parent::__construct();
        $this->addCrumb($this->translate('wiki'), $this->generateUrl('wiki'));

        // Add before filter to show 404 if the wiki is disabled.
        if (!$this->currentProject->enable_wiki) {
            $this->before('*', function () {
                    return $this->show404();
            });
        }

        // Get the page.
        $this->before(['revisions', 'edit', 'save', 'destroy'], function () {
            $this->page = $this->currentProject->wikiPages()
                ->where('slug = :slug')
                ->setParameter('slug', Request::$properties->get('slug'))
                ->fetch();

            if (!$this->page) {
                return $this->show404();
            }

            $this->addCrumb($this->page['title'], routeUrl('wiki_page'));
        });

        // Check permissions
        $this->before(['new', 'create', 'edit', 'save', 'destroy'], function () {
            $action = Request::$properties->get('action');
            if (($action == 'new' || $action == 'create') && !$this->hasPermission('create_wiki_page')) {
                return $this->show403();
            } elseif (($action == 'edit' || $action == 'save') && !$this->hasPermission('edit_wiki_page')) {
                return $this->show403();
            } elseif ($action == 'destroy' && !$this->hasPermission('delete_wiki_page')) {
                return $this->show403();
            }
        });
    }

    /**
     * Pages listing
     */
    public function pagesAction()
    {
        $this->addCrumb($this->translate('pages'), $this->generateUrl('wiki_pages'));

        $pages = $this->currentProject->wikiPages()->fetchAll();

        return $this->respondTo(function ($format) use ($pages) {
            if ($format == 'html') {
                return $this->render('wiki/pages.phtml', ['pages' => $pages]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($pages);
            }
        });
    }

    /**
     * New page.
     *
     * @param string $slug
     */
    public function newAction($slug = null)
    {
        $this->addCrumb($this->translate('new_page'), $this->generateUrl('wiki_new'));

        $page = new WikiPage(['slug' => $slug]);
        $revision = new WikiRevision;

        return $this->render('wiki/new.phtml', [
            'page' => $page,
            'revision' => $revision
        ]);
    }

    public function createAction()
    {
        $this->addCrumb($this->translate('new_page'), $this->generateUrl('wiki_new'));

        $page = new WikiPage($this->pageParams());
        $page->revision_id = 0;

        $revision = new WikiRevision($this->revisionParams());
        $revision['revision'] = 1;

        // Validate page and revision
        $page->validate();
        $revision->validate();

        if (!$page->hasErrors() && !$revision->hasErrors()) {
            $page->save();
            $revision->wiki_page_id = $page->id;
            $revision->save();
            $page->revision_id = $revision->id;
            $page->save();

            return $this->redirectTo('wiki_page', ['slug' => $page['slug']]);
        }

        return $this->render('wiki/new.phtml', [
            'page' => $page,
            'revision' => $revision
        ]);
    }

    /**
     * Show page.
     *
     * @param string $slug
     */
    public function showAction($slug = 'main')
    {
        $page = $this->currentProject->wikiPages()
            ->where('slug = :slug')
            ->setParameter('slug', $slug)
            ->fetch();

        if (!$page) {
            return $this->newAction($slug);
        }

        $this->addCrumb($page['title'], $this->generateUrl('wiki_page', ['slug' => $page['slug']]));

        return $this->respondTo(function ($format) use ($page) {
            if ($format == 'html') {
                return $this->render('wiki/show.phtml', ['page' => $page]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($page->toArray());
            }
        });
    }

    /**
     * Edit wiki page.
     */
    public function editAction()
    {
        $this->addCrumb($this->translate('edit_page'), $this->generateUrl('wiki_edit'));

        return $this->render('wiki/edit.phtml', [
            'page' => $this->page,
            'revision' => $this->page->revision()
        ]);
    }

    /**
     * Save wiki page.
     */
    public function saveAction()
    {
        $this->addCrumb($this->translate('edit_page'), $this->generateUrl('wiki_edit'));

        $revision = new WikiRevision($this->revisionParams() + [
            'wiki_page_id' => $this->page['id'],
            'revision' => $this->page->revision()['revision'] + 1
        ]);

        $this->page->set($this->pageParams());

        // Validate page and revision
        $this->page->validate();
        $revision->validate();

        if (!$this->page->hasErrors() && !$revision->hasErrors()) {
            // Check if the content is different and create the revision if it is
            if ($revision['content'] !== $this->page->revision()['content']) {
                $revision->save();
                $this->page['revision_id'] = $revision['id'];
            }

            $this->page->save();

            return $this->redirectTo('wiki_page', ['slug' => $this->page['slug']]);
        }

        return $this->render('wiki/edit.phtml', [
            'page' => $this->page,
            'revision' => $revision
        ]);
    }

    /**
     * Revisions listing.
     *
     * @param string $slug
     */
    public function revisionsAction()
    {
        $revisions = $this->page->revisions()
            ->addSelect('u.name AS user_name')
            ->addSelect('u.email AS user_email')
            ->leftJoin(
                'wiki_revision',
                User::tableName(),
                'u',
                'wiki_revision.user_id = u.id'
            )
            ->orderBy('revision', 'DESC')
            ->fetchAll();

        $this->addCrumb($this->translate('revisions'), routeUrl('wiki_revisions'));

        return $this->respondTo(function ($format) use ($revisions) {
            if ($format == 'html') {
                return $this->render('wiki/revisions.phtml', [
                    'page' => $this->page,
                    'revisions' => $revisions
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($revisions);
            }
        });
    }

    /**
     * @return array
     */
    protected function pageParams()
    {
        return [
            'project_id' => $this->currentProject['id'],
            'title' => Request::$post['title'],
            'slug' => Request::$post['slug']
        ];
    }

    /**
     * @return array
     */
    protected function revisionParams()
    {
        return [
            'content' => Request::$post['content'],
            'user_id' => $this->currentUser['id']
        ];
    }
}
