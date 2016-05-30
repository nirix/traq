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

        return $this->render('wiki/new.phtml', ['page' => $page]);
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
}
