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

require __DIR__ . '/base.php';

/**
 * Priorities controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminPrioritiesController extends AdminBase
{
	public function __construct()
	{
		parent::__construct();
		$this->title(l('priorities'));
	}

	/**
	 * priority listing.
	 */
	public function action_index()
	{
		View::set('priorities', priority::fetch_all());
	}

	/**
	 * New priority.
	 */
	public function action_new()
	{
		// Create the priority
		$priority = new Priority();

		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Set the name
			$priority->set('name', Request::$post['name']);

			// Save and redirect
			if ($priority->save())
			{
				Request::redirect(Request::base('/admin/priorities'));
			}
		}

		View::set('priority', $priority);
	}

	/**
	 * Edit priority.
	 *
	 * @param integer $id
	 */
	public function action_edit($id)
	{
		// Get the priority
		$priority = Priority::find($id);

		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Set the name
			$priority->set('name', Request::$post['name']);

			// Save and redirect
			if ($priority->save())
			{
				Request::redirect(Request::base('/admin/priorities'));
			}
		}

		View::set('priority', $priority);
	}

	/**
	 * Delete priority.
	 *
	 * @param integer $id
	 */
	public function action_delete($id)
	{
		// Get the priority
		$priority = Priority::find($id);

		// Delete and redirect
		$priority->delete();
		Request::redirect(Request::base('/admin/priorities'));
	}
}