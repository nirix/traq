<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
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

/**
 * Wiki controller
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers
 */
class Wiki extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('wiki'));
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
}
