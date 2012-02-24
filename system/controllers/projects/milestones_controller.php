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

require __DIR__ . "/base.php";

/**
 * Milestones controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ProjectsMilestonesController extends ProjectSettingsBase
{
	public function __construct()
	{
		parent::__construct();
		$this->title(l('milestones'));
	}

	public function action_index()
	{
		View::set('milestones', $this->project->milestones);
	}
	
	public function action_new()
	{
		$this->title(l('new'));

		$milestone = new Milestone();
		
		if (Request::$method == 'post')
		{
			$milestone->set(array(
				'name' => Request::$post['name'],
				'slug' => Request::$post['slug'],
				'info' => Request::$post['info'],
				'project_id' => $this->project->id
			));
			
			if ($milestone->is_valid())
			{
				$milestone->save();
				Request::redirect(Request::base("{$this->project->slug}/settings/milestones"));
			}
		}
		
		View::set('milestone', $milestone);
	}
	
	public function action_edit($id)
	{
		$this->title(l('edit'));

		$milestone = Milestone::find($id);
		
		if (Request::$method == 'post')
		{
			$milestone->set(array(
				'name' => Request::$post['name'],
				'slug' => Request::$post['slug'],
				'info' => Request::$post['info']
			));
			
			if ($milestone->is_valid())
			{
				$milestone->save();
				Request::redirect(Request::base("{$this->project->slug}/settings/milestones"));
			}
		}
		
		View::set('milestone', $milestone);
	}
}