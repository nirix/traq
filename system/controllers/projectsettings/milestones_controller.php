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
 * Milestones controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ProjectSettingsMilestonesController extends ProjectSettingsAppController
{
	public function __construct()
	{
		parent::__construct();
		$this->title(l('milestones'));
	}

	/**
	 * Milestones listing page.
	 */
	public function action_index()
	{
		View::set('milestones', $this->project->milestones);
	}
	
	/**
	 * New milestone page.
	 */
	public function action_new()
	{
		$this->title(l('new'));

		$milestone = new Milestone();
		
		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Set the information
			$milestone->set(array(
				'name' => Request::$post['name'],
				'slug' => Request::$post['slug'],
				'codename' => Request::$post['codename'],
				'info' => Request::$post['info'],
				'project_id' => $this->project->id,
				'displayorder' => Request::$post['displayorder']
			));
			
			// Check if the data is valid
			if ($milestone->is_valid())
			{
				// Save and redirect
				$milestone->save();
				Request::redirect(Request::base("{$this->project->slug}/settings/milestones"));
			}
		}
		
		View::set('milestone', $milestone);
	}
	
	/**
	 * Edit milestone page.
	 *
	 * @param integer $id Milestone ID
	 */
	public function action_edit($id)
	{
		$this->title(l('edit'));

		// Fetch the milestone
		$milestone = Milestone::find($id);
		
		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Update the information
			$milestone->set(array(
				'name' => Request::$post['name'],
				'slug' => Request::$post['slug'],
				'codename' => Request::$post['codename'],
				'info' => Request::$post['info'],
				'displayorder' => Request::$post['displayorder']
			));
			
			// Make sure the data is valid
			if ($milestone->is_valid())
			{
				// Save and redirect
				$milestone->save();
				Request::redirect(Request::base("{$this->project->slug}/settings/milestones"));
			}
		}
		
		View::set('milestone', $milestone);
	}
}