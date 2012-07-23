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
			if (current_user()->permission($project->id, 'view'))
			{
				$projects[] = $project;
			}
		}
		
		// Send the projects array to the view.
		View::set('projects', $projects);
	}
	
	/**
	 * Handles the project info page.
	 */
	public function action_view()
	{
		// Make sure this is a project
		if (!$this->project)
		{
			return $this->show_404();
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
		$milestones = Milestone::select()->where('project_id', $this->project->id);

		// Are we displaying all milestones?
		if (isset(Request::$request['all']))
		{
		}
		// Just the completed ones?
		else if (isset(Request::$request['completed']))
		{
			$milestones = $milestones->where('status', 2);
		}
		// Just the cancelled ones?
		else if (isset(Request::$request['cancelled']))
		{
			$milestones = $milestones->where('status', 0);
		}
		// Looks like just the active ones
		else
		{
			$milestones = $milestones->where('status', 1);
		}

		// Get the milestones and send them to the view
		$milestones = $milestones->order_by('displayorder', 'ASC')->exec()->fetch_all();
		View::set('milestones', $milestones);
	}

	/**
	 * Handles the milestone page.
	 */
	public function action_milestone($milestone_slug)
	{
		// Get the milestone
		$milestone = Milestone::select()->where(array(
			array('project_id', $this->project->id),
			array('slug', $milestone_slug)
		))->exec()->fetch();

		// And send it to the view
		View::set('milestone', $milestone);
	}
	
	/**
	 * Handles the timeline page.
	 */
	public function action_timeline()
	{
		$rows = array();

		// Fetch the different days with a nicely formatted
		// query for everyone to read easily, unlike the one
		// from 2.x and eariler.
		$days_query = Database::connection()->query("
			SELECT
			DISTINCT
				YEAR(created_at) AS 'year',
				MONTH(created_at) AS 'month',
				DAY(created_at) AS 'day',
				created_at

			FROM " . DB_PREFIX . "timeline
			WHERE project_id = '{$this->project->id}'

			GROUP BY
				YEAR(created_at),
				MONTH(created_at),
				DAY(created_at)

			ORDER BY created_at DESC
		");

		// Loop through the days and get their activity
		foreach ($days_query as $info)
		{
			// Construct the array for the day
			$day = array(
				'created_at' => $info['created_at'],
				'activity' => array()
			);

			// Get the date, without the time
			$date = explode(' ', $info['created_at']);
			$date = $date[0];

			// Fetch the activity for this day
			$fetch_activity = Timeline::select()->where('created_at', "{$date} %", "LIKE")->order_by('created_at', 'DESC');
			foreach ($fetch_activity->exec()->fetch_all() as $row)
			{
				// Push it to the days activity array.
				$day['activity'][] = $row;
			}

			// Push the days data to the
			// rows array,
			$rows[] = $day;
		}

		// Send the days array to the view.
		View::set('days', $rows);
	}
}