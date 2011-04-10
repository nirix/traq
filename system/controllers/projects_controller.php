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

class ProjectsController extends AppController
{
	public function index()
	{
		View::set('projects', $this->db->select()->from('projects')->exec()->fetchAll());
	}
	
	public function view()
	{
		if(!isset($this->project['id'])) $this->_view = 'error/notfound';
	}
	
	public function roadmap()
	{
		$milestones = Load::model('milestones');
		View::set('milestones', $milestones->fetchAll(array('where'=>array('project_id'=>$this->project['id']),'orderby'=>array('displayorder','ASC'))));
	}
	
	public function timeline()
	{
		
	}
	
	public function milestone()
	{
		$milestones = Load::model('milestones');
		View::set('milestone', $milestones->find(array('where'=>array('project_id'=>$this->project['id'],'slug'=>Request::seg(2)))));
	}
}