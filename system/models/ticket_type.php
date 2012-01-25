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

class TicketType extends Model
{
	protected static $_name = 'ticket_types';
	protected static $_properties = array(
		'id',
		'name',
		'bullet',
		'changelog',
		'template'
	);
	
	public function is_valid()
	{
		$errors = array();
		
		// Check if the name is set
		if (empty($this->_data['name']))
		{
			$errors['name'] = l('error:name_blank');
		}
		
		// Check if the bullet is set
		if ($this->_data['changelog'] and empty($this->_data['bullet']))
		{
			$errors['bullet'] = l('error:ticket_type:bullet_blank');
		}
		
		$this->errors = $errors;
		return !count($errors);
	}
}