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

class TicketStatus extends Model
{
	protected static $_name = 'ticket_status';
	protected static $_properties = array(
		'id',
		'name',
		'status',
		'changelog'
	);

	// Checks if the model data is valid
	public function is_valid()
	{
		$errors = array();

		// Make sure the name is set.
		if (empty($this->_data['name']))
		{
			$errors['name'] = l('errors.name_blank');
		}

		$this->errors = $errors;
		return !count($errors) > 0;
	}
}