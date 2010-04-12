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
		else
			$this->location = $location.'/';
	}
	
	/**
	 * List
	 * Returns an array of the requested directory.
	 * @param string $dir The directroy in the repository.
	 */
	public function ls($dir='')
	{
		$dir = $this->cleandirname($dir);
		
		// Exec the command
		$info = exec("svn ls --xml ".$this->location.$dir,$_a,$_r);
		
		// Loop through the entries
		$array = array();
		$xml = new SimpleXMLElement(implode('',$_a));
		foreach($xml->list->entry as $entry)
		{
			$_a = null;
			
			// Get the entry data
			$attribs = $entry->attributes();
			
			// Get log message
			$log = exec("svn log --xml ".$this->location.$dir." -r ".(int)$entry->commit['revision'],$_a,$_r);
			$loginfo = simplexml_load_string(implode('',$_a));
			
			$info = array();
			$info['name'] = (string)$entry->name;
			$info['size'] = (int)$entry->size;
			$info['kind'] = (string)$attribs['kind'];
			$info['path'] = (string)(empty($dir) ? $info['name'] : $dir.'/'.$info['name']);
			$info['commit'] = array(
				'author' => (string)$loginfo->logentry->author,
				'date' => (string)$entry->commit->date,
				'rev' => (int)$entry->commit['revision'],
				'msg' => (string)$loginfo->logentry->msg
				);
			$array[] = $info;
		}
		
		return $array;
	}
	
	// Used to clean the directory name.
	private function cleandirname($dir)
	{
		if(empty($dir)) return;
		return (substr($dir,-1) == '/' ? $dir : $dir.'/');
	}
}
?>