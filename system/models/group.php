<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
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

class Group extends Model
{
	protected static $_name = 'usergroups';
	protected static $_properties = array(
		'id',
		'name',
		'is_admin',
		'create_tickets',
		'update_tickets',
		'comment_tickets',
		'delete_tickets',
		'add_attachments'
	);
	
	/**
	 * Returns an array of groups to be used
	 * with the Form::select() method.
	 *
	 * @return array
	 */
	public function select_options()
	{
		$options = array();
		foreach (static::fetch_all() as $group)
		{
			$options[] = array('value' => $group->id, 'text' => $group->name);
		}
		return $options;
	}
	
	/**
	 * Checks if the groups data is valid.
	 *
	 * @return bool
	 */
	public function is_valid()
	{
		$errors = array();
		
		// Make sure the name is set...
		if (empty($this->_data['name']))
		{
			$errors['name'] = l('error:name_blank');
		}
		
		$this->errors = $errors;
		return !count($errors) > 0;
	}
}