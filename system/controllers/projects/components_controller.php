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
 * Components controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class ProjectsComponentsController extends ProjectSettingsBase
{
	public function __construct()
	{
		parent::__construct();
		$this->title(l('components'));
	}

	public function action_index()
	{
		View::set('components', $this->project->components);
	}
	
	public function action_new()
	{
		$this->title(l('new'));

		$component = new Component();
		
		if (Request::$method == 'post')
		{
			$component->set(array(
				'name' => Request::$post['name'],
				'project_id' => $this->project->id
			));
			
			if ($component->is_valid())
			{
				$component->save();
				Request::redirect(Request::base("{$this->project->slug}/settings/components"));
			}
		}
		
		View::set('component', $component);
	}
	
	public function action_edit($id)
	{
		$this->title(l('edit'));

		$component = Component::find($id);
		
		if (Request::$method == 'post')
		{
			$component->set(array(
				'name' => Request::$post['name'],
			));
			
			if ($component->is_valid())
			{
				$component->save();
				Request::redirect(Request::base("{$this->project->slug}/settings/components"));
			}
		}
		
		View::set('component', $component);
	}
}