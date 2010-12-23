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
 */

class URI
{
	public $seg = array();
	public $style = 1;
	private $root;
	private $request;
	private $file = 'index.php';
	
	// Construct function.
	public function __construct()
	{
		if(strpos($_SERVER['REQUEST_URI'],$this->file))
			$this->request = trim(str_replace($_SERVER['SCRIPT_NAME'],'',$_SERVER['REQUEST_URI']),'/');
		else
			$this->request = trim(str_replace(str_replace($this->file,'',$_SERVER['SCRIPT_NAME']),'',$_SERVER['REQUEST_URI']),'/');
		
		$this->request = str_replace('?'.$_SERVER['QUERY_STRING'],'',$this->request);
		
		$this->seg = explode('/',$this->request);
	}
	
	/**
	 * Get URI
	 * Used to get the current URI
	 */
	public function geturi()
	{
		return $this->anchor($this->seg);
	}
	
	public function seg($seg)
	{
		if(isset($this->seg[$seg])) return $this->seg[$seg];
		return false;
	}
	
	public function anchorfile() { return $this->file; }
	
	/**
	 * Anchor
	 * Used to create URI's
	 */
	public function anchor($segments = array())
	{
		if(!is_array($segments))
			$segments = func_get_args();
		
		$path = ($this->style == 1 ? str_replace($this->file,'',$_SERVER['SCRIPT_NAME']) : $_SERVER['SCRIPT_NAME'].'/');
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