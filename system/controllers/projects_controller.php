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
		$milestones = Milestone::select()->where('project_id', $this->project->id)->order_by('displayorder', 'ASC')->exec()->fetch_all();
		View::set('milestones', $milestones);
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
				YEAR(timestamp) AS 'year',
				MONTH(timestamp) AS 'month',
				DAY(timestamp) AS 'day',
				timestamp

			FROM " . DB_PREFIX . "timeline
			WHERE project_id = '{$this->project->id}'

			GROUP BY
				YEAR(timestamp),
				MONTH(timestamp),
				DAY(timestamp)

			ORDER BY timestamp DESC
		");

		// Loop through the days and get their activity
		foreach ($days_query as $info)
		{
			// Construct the array for the day
			$day = array(
				'timestamp' => $info['timestamp'],
				'activity' => array()
			);

			// Get the date, without the time
			$date = explode(' ', $info['timestamp']);
			$date = $date[0];

			// Fetch the activity for this day
			$fetch_activity = Timeline::select()->where('timestamp', "{$date} %", "LIKE")->order_by('timestamp', 'DESC');
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