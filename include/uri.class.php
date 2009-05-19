<?php
/**
 * URI Class
 * @author Jack Polgar <xocide@gmail.com>
 * @copyright Copyright (c)2009 Jack Polgar
 * @version 1.0
 * Copyright (c) 2009, Jack Polgar
 * All rights reserved.
 */
class URI
{
	public $seg = array();
	public $type = 1; // 1 = mysite.com/nice/uri; 2 = mysite.com/index.php/nice/uri
	private $anchorfile = NULL;
	
	// Construct function.
	public function __construct()
	{
		$pathinfo = trim($_SERVER['PATH_INFO'],'/');
		$segments = explode('/',$pathinfo);
		$this->seg = $segments;
		$this->anchorfile = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_BASENAME);
	}
	
	/**
	 * Get URI
	 * Used to get the current URI
	 * @since 0.1
	 */
	public function geturi()
	{
		return $this->anchor().implode('/',$this->seg).'/';
	}
	
	/**
	 * Anchor
	 * Used to create URI's
	 * @since 0.1
	 */
	public function anchor($segments = array())
	{
		if(!is_array($segments)) {
			$segments = func_get_args();
		}
		$path = ($this->type == 1 ? str_replace($this->anchorfile,'',$_SERVER['SCRIPT_NAME']) : $_SERVER['SCRIPT_NAME'].'/');
		return $path.$this->array_to_uri($segments);
	}
	
	/**
	 * Get Root Path
	 * Returns the path of the main file.
	 * @return string
	 */
	public function rootpath()
	{
		return str_replace($this->anchorfile,'',$_SERVER['SCRIPT_NAME']);
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
		return implode('/',$segs).'/';
	}
}
?>