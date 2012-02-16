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

class Project extends Model
{
	protected static $_name = 'projects';
	protected static $_properties = array(
		'id',
		'name',
		'codename',
		'slug',
		'info',
		'managers',
		'private',
		'next_tid',
		'displayorder'
	);
	
	// Has-many relationships with other models
	protected static $_has_many = array(
		'tickets', 'milestones', 'components',
		'wiki_pages' => array('model' => 'WikiPage')
	);
	
	protected static $_filters_after = array(
		'construct' => array('process_managers')
	);
	
	
	public function href($uri = null)
	{
		return $this->_data['slug'] . ($uri !== null ? '/' . implode('/', func_get_args()) : '');
	}
	
	/**
	 * Check if the specified user has permission to manage the project.
	 *
	 * @param object $user
	 *
	 * @return bool
	 */
	public function is_manager($user)
	{
		return in_array($user->id, $this->_managers);
	}
	
	/**
	 * Returns a the project managers as an array.
	 *
	 * @return array
	 */
	public function managers()
	{
		return $this->_managers;
	}
	
	/**
	 * Checks if the model data is valid.
	 *
	 * @return bool
	 */
	public function is_valid()
	{
		$errors = array();
		
		// Check if the name is empty
		if (empty($this->_data['name']))
		{
			$errors['name'] = l('error:name_blank');
		}
		
		// Check if the slug is empty
		if (empty($this->_data['slug']))
		{
			$errors['slug'] = l('error:slug_blank');
		}
		
		$this->errors = $errors;
		return !count($errors) > 0;
	}
	
	/**
	 * Turns the managers value into an array.
	 */
	protected function process_managers()
	{
		$this->_managers = explode(',', $this->_data['managers']);
	}
}