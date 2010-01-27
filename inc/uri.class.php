<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

class URI
{
	public $seg = array();
	public $style = 1;
	private $anchorfile = NULL;
	private $singleproject = false;
	
	// Construct function.
	public function __construct()
	{
		$pathinfo = trim($_SERVER['PATH_INFO'],'/');
		$this->seg = explode('/',$pathinfo);
		$this->anchorfile = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_BASENAME);
	}
	
	/**
	 * Get URI
	 * Used to get the current URI
	 */
	public function geturi()
	{
		return $this->anchor($this->seg);//.implode('/',$this->seg);
	}
	
	/**
	 * Anchor
	 * Used to create URI's
	 */
	public function anchor($segments = array())
	{
		if(!is_array($segments)) {
			$segments = func_get_args();
		}
		
		if($segments[0] == PROJECT_SLUG && $this->singleproject) unset($segments[0]);
		
		$path = ($this->style == 1 ? str_replace($this->anchorfile,'',$_SERVER['SCRIPT_NAME']) : $_SERVER['SCRIPT_NAME'].'/');
		return $path.$this->array_to_uri($segments);
	}
	
	/**
	 * Single Project
	 * Configure the URI class for a single project Traq setup.
	 */
	public function singleproject()
	{
		if($this->seg[0] != 'user') {
			$args = array();
			$args[] = PROJECT_SLUG;
			foreach($this->seg as $seg)
			{
				$args[] = $seg;
			}
			$this->singleproject = true;
			$this->seg = $args;
		}
	}
	
	// Used to convert the array passed to it into a URI
	private function array_to_uri($segments = array())
	{
		if(count($segments) < 1
		or !is_array($segments))
		{
			return;
		}
		foreach($segments as $key => $val)
		{
			$segs[] = $val;
		}
		return implode('/',$segs);
	}
}
?>