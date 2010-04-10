<?php
/**
 * Traq 2
 * Copyright (c) 2009, 2010 Jack Polgar
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
 *
 * $Id$
 */

class Subversion extends Source
{
	public function __construct($location)
	{
		if(substr($location,0,1) == '/')
			$this->location = substr($location,1);
	}
	
	/**
	 * Get
	 * Gets the information for the specified location in the repository.
	 * @param string $location The filename or directroy in the repository.
	 */
	public function get($location)
	{
	
	}
}
?>