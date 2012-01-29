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
		$page = $this->project->wiki_pages->where('slug', $slug)->exec()->fetch();
		View::set('page', $page);
	}

	public function action_pages()
	{
		$pages = $this->project->wiki_pages->exec()->fetch_all();
		View::set('pages', $pages);
	}

	public function action_new()
	{
		$this->title(l('new'));
	}

	public function action_edit($id)
	{
		$this->title(l('edit'));
	}

	public function action_delete($id)
	{
		
	}
}