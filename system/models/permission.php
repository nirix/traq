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
	protected static $_properties = array(
		'id',
		'group_id',
		'project_id',
		'action',
		'value'
	);

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
		// Fetch the permission rows and merge them with the defaults
		$rows = static::select()->where('group_id', $group_id)->where('project_id', $project_id)->exec()->fetch_all();
		$rows = array_merge(static::defaults($group_id), $rows);

		// Loop over the permissions and make it
		// easy to access the permission values.
		$permissions = array();
		foreach ($rows as $permission)
		{
			$permissions[$permission->action] = $permission->value;
		}

		// And return them...
		return $permissions;
	}

	/**
	 * Returns the default permissions.
	 *
	 * @param integer $group_id
	 *
	 * @return array
	 */
	public static function defaults($group_id = 0)
	{
		// Fetch the defaults
		$defaults = static::select()->where('group_id', $group_id)->exec()->fetch_all();

		// If we're fetching a specific group,
		// also fetch the defaults for all groups.
		if ($group_id > 0)
		{
			$defaults = array_merge(static::defaults(), $defaults);
		}

		// Loop over the defaults and push them to a new array
		// this will stop duplicates from the overall defaults
		// and the defaults for specific groups.
		$permissions = array();
		foreach ($defaults as $permission)
		{
			$permissions[$permission->action] = $permission;
		}

		// And return them...
		return $defaults;
	}
}