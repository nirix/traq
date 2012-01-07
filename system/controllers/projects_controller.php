<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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
 * Project controller.
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ProjectsController extends AppController
{
	/**
	 * Project listing page.
	 */
	public function action_index()
	{
		// Fetch all projects and make sure the user has permission
		// to access the project then pass them to the view.
		$projects = array();
		foreach (Project::fetch_all() as $project)
		{
			// Check if the user has access to view the project...
			if ($project->permission($this->user->group_id, 'view'))
			{
				$projects[] = $project;
			}
		}
		
		View::set('projects', $projects);
	}
	
	/**
	 * Handles the project info page.
	 */
	public function action_view()
	{
		if (!$this->project->name)
		{
			$this->show_404();
		}
		
		// Get open and closed ticket counts.
		View::set('ticket_count', array(
			'open' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 0)->exec()->row_count(),
			'closed' => Ticket::select()->where('project_id', $this->project->id)->where('is_closed', 1)->exec()->row_count()
		));
	}
	
	/**
	 * Handles the roadmap page.
	 */
	public function action_roadmap()
	{
		// Get the projects milestones and send them to the view.
		$milestones = Milestone::select()->where('project_id', $this->project->id)->orderBy('displayorder', 'ASC')->exec()->fetch_all();
		View::set('milestones', $milestones);
	}
	
	/**
	 * Handles the timeline page.
	 */
	public function action_timeline()
	{
		
	}
}