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
use Traq\Models\WikiPage;
use Traq\Models\WikiRevision;
use Traq\Models\Timeline;

/**
 * Wiki controller
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 3.0.0
 */
class Wiki extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('wiki'));

        $this->before(
            ['new', 'create', 'edit', 'save', 'delete', 'destroy'],
            [$this, 'checkPermission']
        );

        $this->before(
            ['edit', 'save', 'delete', 'destroy'],
            [$this, 'getPage']
        );
    }

    /**
     * New page.
     *
     * @param string $slug slug for new page
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
     * Create page.
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

            // Create timeline event
            Timeline::wikiPageCreatedEvent($this->currentUser, $page)->save();

            return $this->redirectTo('wiki_page', ['slug' => $page['slug']]);
        } else {
            return $this->render('wiki/new.phtml', [
                'page' => $page
            ]);
        }
    }

    /**
     * Edit page.
     *
     * @param string $slug
     */
    public function editAction($slug)
    {
        $this->title($this->translate('edit'));
        return $this->render('wiki/edit.phtml');
    }

    /**
     * Save page.
     *
     * @param string $slug
     */
    public function saveAction($slug)
    {
        $this->page->set($this->pageParams());

        if (Request::$post->get('content') != $this->page->revision()->content) {
            $revision = new WikiRevision([
                'wiki_page_id' => $this->page->id,
                'revision'     => $this->page->revision()->revision + 1,
                'content'      => Request::$post->get('content'),
                'user_id'      => $this->currentUser['id']
            ]);
        }

        if ($this->page->save()) {
            if (isset($revision)) {
                $revision->save();
                $this->page->revision_id = $revision->id;
            }

            $this->page->save();

            return $this->redirectTo('wiki_page', ['slug' => $this->page['slug']]);
        } else {
            return $this->render('wiki/edit.phtml');
        }
    }

    /**
     * Displays the requested wiki page.
     */
    public function showAction($slug)
    {
        $page = queryBuilder()->select('p.*', 'r.content')
            ->from(PREFIX . 'wiki_pages', 'p')
            ->where('p.project_id = ?')
            ->andWhere('p.slug = ?')
            ->join('p', PREFIX . 'wiki_revisions', 'r', 'r.id = p.revision_id')
            ->setParameter(0, $this->currentProject['id'])
            ->setParameter(1, $slug)
            ->execute()
            ->fetch();

        if (!$page) {
            return $this->newAction($slug);
        }

        $this->title($page['title']);
        $this->set('page', $page);
        return $this->render('wiki/show.phtml');
    }

    /**
     * Displays all the wiki pages for the project.
     */
    public function pagesAction()
    {
        $pages = queryBuilder()->select('p.*')
            ->from(PREFIX . 'wiki_pages', 'p')
            ->where('p.project_id = ?')
            ->orderBy('title', 'ASC')
            ->setParameter(0, $this->currentProject['id'])
            ->execute()
            ->fetchAll();

        return $this->render('wiki/pages.phtml', ['pages' => $pages]);
    }

    /**
     * Page revisions listing.
     */
    public function revisionsAction($slug)
    {
        $page = queryBuilder()->select('p.*')
            ->from(PREFIX . 'wiki_pages', 'p')
            ->where('p.project_id = ?')
            ->andWhere('p.slug = ?')
            ->setParameter(0, $this->currentProject['id'])
            ->setParameter(1, $slug)
            ->execute()
            ->fetch();

        $revisions = queryBuilder()->select('r.*', 'u.name AS user_name', 'u.email AS user_email')
            ->from(PREFIX . 'wiki_revisions', 'r')
            ->where('r.wiki_page_id = ?')
            ->join('r', PREFIX . 'users', 'u', 'u.id = r.user_id')
            ->orderBy('id', 'ASC')
            ->setParameter(0, $page['id'])
            ->execute()
            ->fetchAll();

        $this->title($page['title']);
        $this->title($this->translate('revisions'));

        $this->set('page', $page);
        return $this->render('wiki/revisions.phtml', [
            'revisions' => $revisions
        ]);
    }

    /**
     * View revision.
     *
     * @param string  $slug
     * @param integer $revision
     */
    public function revisionAction($slug, $id)
    {
        $page = queryBuilder()->select('p.*', 'r.content', 'r.revision')
            ->from(PREFIX . 'wiki_pages', 'p')
            ->where('p.project_id = :project_id')
            ->andWhere('p.slug = :slug')
            ->join(
                'p',
                PREFIX . 'wiki_revisions',
                'r',
                'r.wiki_page_id = p.id AND r.revision = :revision'
            )
            ->setParameter('project_id', $this->currentProject['id'])
            ->setParameter('slug', $slug)
            ->setParameter('revision', $id)
            ->execute()
            ->fetch();

        $this->title($page['title']);
        $this->title($this->translate('revisions'));
        $this->title($this->translate('revision_x', $page['revision']));

        $this->set('page', $page);
        return $this->render('wiki/show.phtml');
    }

    /**
     * Delete page.
     *
     * @param string $slug
     */
    public function destroyAction($slug)
    {
        $this->page->delete();
        return $this->redirectTo('wiki_pages', ['pslug' => $this->currentProject['slug']]);
    }

    /**
     * Get submitted page data.
     *
     * @return array
     */
    protected function pageParams()
    {
        return [
            'title'      => Request::$post->get('title'),
            'slug'       => Request::$post->get('slug'),
            'content'    => Request::$post->get('content'),
            'project_id' => $this->currentProject['id'],
            'user_id'    => $this->currentUser->id
        ];
    }

    /**
     * Get current page.
     */
    public function getPage()
    {
        $this->page = WikiPage::where('slug = ?')->andWhere('project_id = ?')
            ->setParameter(0, Request::$properties->get('slug'))
            ->setParameter(1, $this->currentProject['id'])
            ->fetch();

        if (!$this->page) {
            return $this->show404();
        }

        $this->title($this->translate($this->page->title));

        $this->set('page', $this->page);
    }

    /**
     * Check permissions.
     */
    public function checkPermission()
    {
        $action = (Request::$properties->get('action') == 'new' ? 'create' : Request::$properties->get('action'));

        // Check if the user has permission
        if (!$this->hasPermission("{$action}_wiki_page")) {
            // oh noes! display the no permission page.
            return $this->show403();
        }
    }
}
