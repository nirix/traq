<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

/**
 * Wiki controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class WikiController extends AppController
{
	public $_before = array(
		'new' => array('_check_permission'),
		'edit' => array('_check_permission'),
		'delete' => array('_check_permission')
	);

	public function __construct()
	{
		parent::__construct();
		$this->title(l('wiki'));
	}

	public function action_index()
	{
		$main = $this->project->wiki_pages->where('main', 1)->exec()->fetch();
		View::set('page', $main);
		$this->_render['view'] = 'wiki/view';
	}

	public function action_view($slug)
	{
		$page = $this->project->wiki_pages->where('slug', $slug)->exec();

		if (!$page->row_count())
		{
			return current_user()->permission($this->project->id, 'create_wiki_page') ? $this->_new_page($slug) : $this->show_404();
		}

		View::set('page', $page->fetch());
	}

	public function action_pages()
	{
		$pages = $this->project->wiki_pages->exec()->fetch_all();
		View::set('pages', $pages);
	}

	public function action_new($slug = null)
	{
		$this->title(l('new'));

		$page = new WikiPage(array('slug' => $slug));

		if (Request::$method == 'post')
		{
			$page->set(array(
				'title' => Request::$post['title'],
				'slug' => Request::$post['slug'],
				'body' => Request::$post['body']
			));
		}

		View::set('page', $page);
	}

	public function action_edit($slug)
	{
		$this->title(l('edit'));

		$page = $this->project->wiki_pages->where('slug', $slug)->exec()->fetch();

		View::set('page', $page);
	}

	public function action_delete($id)
	{
		
	}

	private function _new_page($slug)
	{
		$this->_render['view'] = 'wiki/new';
		$this->action_new($slug);
	}

	public function _check_permission($action)
	{
		$action = ($action == 'new' ? 'create' : $action);

		if (!current_user()->permission($this->project->id, "{$action}_wiki_page"))
		{
			$this->show_no_permission();
		}
	}
}