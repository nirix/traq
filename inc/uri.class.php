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

class URI
{
	public $seg = array();
	public $style = 1;
	private $anchorfile = NULL;
	
	// Construct function.
	public function __construct()
	{
		$this->seg = explode('/',trim(($_SERVER['PATH_INFO'] != '' ? $_SERVER['PATH_INFO'] : $_SERVER['ORIG_PATH_INFO']),'/'));
		$this->anchorfile = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_BASENAME);
	}
	
	/**
	 * Get URI
	 * Used to get the current URI
	 */
	public function geturi()
	{
		return $this->anchor($this->seg);
	}
	
	/**
	 * Anchor
	 * Used to create URI's
	 */
	public function anchor($segments = array())
	{
		if(!is_array($segments))
			$segments = func_get_args();
		
		$path = ($this->style == 1 ? str_replace($this->anchorfile,'',$_SERVER['SCRIPT_NAME']) : $_SERVER['SCRIPT_NAME'].'/');
		return $path.$this->array_to_uri($segments);
	}
	
	// Used to convert the array passed to it into a URI
	private function array_to_uri($segments = array())
	{
		if(count($segments) < 1 or !is_array($segments)) return;
		
		foreach($segments as $key => $val)
			$segs[] = $val;
			
		return implode('/',$segs);
	}
}
?>