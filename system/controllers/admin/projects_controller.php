<?php
/*
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
 * Admin Projects controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminProjectsController extends AdminBase
{
	public function __construct()
	{
		parent::__construct();
		$this->title(l('projects'));
	}

	public function action_index()
	{
		// Fetch all projects and pass them to the view.
		$projects = Project::fetch_all();
		View::set('projects', $projects);
	}
	
	/**
	 * Create a new project page.
	 */
	public function action_new()
	{
		$this->title(l('new'));
		
		$project = new Project;
		
		if (Request::$method == 'post')
		{
			$project->set(array(
				'name' => Request::$post['name'],
				'slug' => Request::$post['slug'],
				'info' => Request::$post['info']
			));
			
			if ($project->is_valid())
			{
				$project->save();
				Request::redirect(Request::base('admin'));
			}
		}
		
		View::set('proj', $project);
	}
	
	/**
	 * Delete a project.
	 *
	 * @param integer $id Project ID.
	 */
	public function action_delete($id)
	{
		$project = Project::find('id', $id);
		$project->delete();
		Request::redirect(Request::base('admin'));
	}
}