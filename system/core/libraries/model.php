<?php
/**
 * Meridian
 * Copyright (C) 2010-2011 Jack Polgar
 * 
 * This file is part of Meridian.
 * 
 * Meridian is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Meridian is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Meridian. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Database model base class
 * @package Meridian
 */
class Model
{
	public $_table;
	
	public function __construct($name)
	{
		$this->_table = $name;
	}
	
	/**
	 * Fetches all tickets matching the arguments given.
	 * @param array $args Array of arguments/filters, such as array('where'=>array(...))
	 * @return array
	 */
	public function fetchAll(array $args = array())
	{
		$fetch = $this->db->select()->from($this->_table);
		
		if(isset($args['where'])) $fetch->where($args['where']);
		if(isset($args['orderby'])) $fetch->orderby($args['orderby'][0], $args['orderby'][1]);
		
		return $fetch->exec()->fetch_all();
	}
	
	/**
	 * Fetches an array of a row matching the arguments given.
	 * @param array $args Array of arguments/filters, such as array('where'=>array(...))
	 * @return array
	 */
	public function find(array $args)
	{
		$fetch = $this->db->select()->from($this->_table);
		
		if(isset($args['where'])) $fetch->where($args['where']);
		if(isset($args['orderby'])) $fetch->orderby($args['orderby'][0], $args['orderby'][1]);
		
		return $fetch->exec()->fetch_assoc();
	}
}