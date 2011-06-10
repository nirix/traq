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
 * MySQL Improved Statement handler
 * @package Meridian
 * @subpackage Database
 */
class MySQLi_Statement
{
	public function __construct($result)
	{
		$this->result = $result;
	}
	
	public function fetchArray()
	{
		return mysqli_fetch_array($this->result);
	}
	public function fetch_array()
	{
		return $this->fetchArray();
	}
	
	public function fetchAssoc()
	{
		return mysqli_fetch_assoc($this->result);
	}
	public function fetch_assoc()
	{
		return $this->fetchAssoc();
	}
	
	public function fetchAll()
	{
		$rows = array();
		while($row = $this->fetch_assoc())
		{
			$rows[] = $row;
		}
		return $rows;
	}
	public function fetch_all()
	{
		return $this->fetchAll();
	}
	
	public function numRows()
	{
		return mysqli_num_rows($this->result);
	}
	public function num_rows()
	{
		return $this->numRows();
	}
}