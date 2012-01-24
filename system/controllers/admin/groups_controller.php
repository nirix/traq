<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
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
 * Admin Groups controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminGroupsController extends AdminBase
{
	public function action_index()
	{
		$groups = Group::fetch_all();
		View::set('groups', $groups);
	}
	
	/**
	 * New group page.
	 */
	public function action_new()
	{
		// Create a new group object.
		$group = new Group;
		
		// Check if the form has been submitted.
		if (Request::$method == 'post')
		{
			// Set the groups name.
			$group->set('name', Request::$post['name']);
			
			// Make sure the data is valid.
			if ($group->is_valid())
			{
				// Save and redirect.
				$group->save();
				Request::redirect(Request::base('/admin/groups'));
			}
		}
		
		// Send the group object to the view.
		View::set('group', $group);
	}
	
	/**
	 * Edit group page.
	 *
	 * @param integer $id Group ID.
	 */
	public function action_edit($id)
	{
		// Find the group.
		$group = Group::find($id);
		
		// Check if the form has been submitted.
		if (Request::$method == 'post')
		{
			// Set the groups name
			$group->set('name', Request::$post['name']);
			
			// Make sure the data is valid.
			if ($group->is_valid())
			{
				// Save and redirect.
				$group->save();
				Request::redirect(Request::base('/admin/groups'));
			}
		}
		
		// Send the group object to the view.
		View::set('group', $group);
	}
	
	/**
	 * Delete group page.
	 *
	 * @param integer $id Group ID.
	 */
	public function action_delete($id)
	{
		// Find the group, delete it and redirect
		$group = Group::find($id);
		$group->delete();
		Request::redirect(Request::base('/admin/groups'));
	}
}