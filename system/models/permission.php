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
 * Permission model.
 *
 * @package Traq
 * @subpackage Models
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Permission extends Model
{
	protected static $_name = 'permissions';

	/*
	This is what the permission table may look like soon,
	store each permission as its own row to allow for even more
	permission inheritance.

	Also to make it easier plugins to add permissions.

	protected static $_properties => array(
		'id',
		'group_id',
		'project_id',
		'action',
		'value'
	);
	*/

	/**
	 * Returns the permissions for the group and project.
	 *
	 * @param integer $group_id
	 * @param integer $project_id
	 *
	 * @return array
	 */
	public static function get_permissions($group_id, $project_id = 0)
	{
		// Query the permissions table
		$project_permissions = static::select()->where('group_id', $group_id)->where('project_id', $project_id)->exec();

		// Check if theres a permission set for this project..
		if ($project_permissions->row_count())
		{
			return $project_permissions->fetch()->__toArray();
		}
		// Nope, fetch default set
		else
		{
			return static::select()->where('group_id', $group_id)->where('project_id', 0)->exec()->fetch()->__toArray();
		}
	}
}