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
	// Before filters
	public $_before = array(
		'new' => array('_check_permission'),
		'edit' => array('_check_permission'),
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
	 * Displays the main wiki page for the project.
	 */
	public function action_index()
	{
		$this->_render['view'] = 'wiki/view';
		$this->action_view('main');
	}

	/**
	 * Displays the requested wiki page.
	 *
	 * @param string $slug Wiki page slug.
	 */
	public function action_view($slug)
	{
		// Get the page
		$page = $this->project->wiki_pages->where('slug', $slug)->exec();

		// Check if the page exists
		if (!$page->row_count())
		{
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
	 *
	 * @param string $slug The slug for the wiki page.
	 */
	public function action_new($slug = null)
	{
		$this->title(l('new'));

		// Fetch the page from the database
		$page = new WikiPage(array('slug' => $slug));

		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Update the page information
			$page->set(array(
				'title' => Request::$post['title'],
				'slug' => Request::$post['slug'],
				'body' => Request::$post['body'],
				'project_id' => $this->project->id
			));

			// Save and redirect
			if ($page->save())
			{
				Request::redirect(Request::base($page->href()));
			}
		}

		View::set('page', $page);
	}

	/**
	 * Displays the edit wiki page form.
	 *
	 * @param string $slug The slug for the wiki page.
	 */
	public function action_edit($slug)
	{
		$this->title(l('edit'));

		// Fetch the page from the database
		$page = $this->project->wiki_pages->where('slug', $slug)->exec()->fetch();

		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Update the page information
			$page->set(array(
				'title' => Request::$post['title'],
				'slug' => Request::$post['slug'],
				'body' => Request::$post['body'],
				'project_id' => $this->project->id
			));

			// Save and redirect
			if ($page->save())
			{
				Request::redirect(Request::base($page->href()));
			}
		}

		View::set('page', $page);
	}

	/**
	 * Deletes the specified wiki page.
	 *
	 * @param string $slug The slug for the wiki page.
	 */
	public function action_delete($slug)
	{
		// Get the page
		$page = $this->project->wiki_pages->where('slug', $slug)->exec()->fetch();

		// Delete the page
		$page->delete();

		// Redirect to main page
		Request::redirect(Request::base($this->project->href('wiki')));
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
		$this->_render['view'] = 'wiki/new';
		$this->action_new($slug);
	}

	/**
	 * Used to check the permission for the requested action.
	 */
	public function _check_permission()
	{
		$action = (Router::$method == 'new' ? 'create' : Router::$method);

		// Check if the user has permission
		if (!current_user()->permission($this->project->id, "{$action}_wiki_page"))
		{
			// oh noes! display the no permission page.
			$this->show_no_permission();
			return false;
		}
	}
}