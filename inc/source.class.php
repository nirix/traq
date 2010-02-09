<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

class Source
{
	private $location;
	
	// Constructor.
	public function __construct($location)
	{
		$this->location = $location;
	}
	
	/**
	 * Cache
	 * Caches the data in JSON format.
	 * @param string $filename The name of the file to cache the data, example: r231.trunk-inc-lang.json
	 * @param array $data The data to cache in an array.
	 */
	private function cache($filename,$data)
	{
		
	}
}
?>