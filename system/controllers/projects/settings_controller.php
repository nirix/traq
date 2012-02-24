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
 * Project settings controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ProjectsSettingsController extends ProjectSettingsBase
{
	public function action_index()
	{
		$project = clone $this->project;
		
		if (Request::$method == 'post')
		{
			$project->set(array(
				'name' => Request::$post['name'],
				'slug' => Request::$post['slug'],
				'codename' => Request::$post['codename'],
				'info' => Request::$post['info']
			));
			
			if ($project->is_valid())
			{
				$project->save();
				Request::redirect(Request::base($project->href('settings')));
			}
		}
		
		View::set('proj', $project);
	}
}