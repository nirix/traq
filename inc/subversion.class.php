<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
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

class Subversion extends Source
{
	public $mimetypes = array(
		'.txt' => 'text/plain',
		'.htm' => 'text/plain',
		'.html' => 'text/plain',
		'.php' => 'text/plain',
		'.lua' => 'text/plain',
		'.css' => 'text/css',
		'.js' => 'text/plain',
		'.png' => 'image/png',
		'.jpe' => 'image/jpeg',
		'.jpeg' => 'image/jpeg',
		'.jpg' => 'image/jpeg',
		'.gif' => 'image/gif',
		'.bmp' => 'image/bmp',
		);
	
	public function __construct($location,$user=null,$pass=null)
	{
		if(substr($location,0,1) == '/')
			$this->location = substr($location,1);
		else
			$this->location = $location.'/';
		
		$this->username = $user;
		$this->password = $pass;
	}
	
	private function userpass()
	{
		if($this->username == null and $this->password == null) return false;
		return ' --username '. $this->username.' --password '.$this->password.' --non-interactive';
	}
	
	/**
	 * List Directory
	 * Returns an array of the requested directory.
	 *
	 * @param string $dir The directroy in the repository.
	 * @return array
	 */
	public function listdir($dir='')
	{
		$dir = $this->cleandirname($dir);
		
		// Exec the command
		$descriptorspec = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
		$process = proc_open("svn ls --xml ".$this->location.$dir.$this->userpass(), $descriptorspec, $pipes);
		if(is_resource($process))
		{
			fclose($pipes[0]);
			$contents = stream_get_contents($pipes[1]);
		
			// Loop through the entries
			$files = array('dirs'=>array(),'files'=>array());
			$xml = new SimpleXMLElement($contents);
			foreach($xml->list->entry as $entry)
			{
				// Get the entry data
				$attribs = $entry->attributes();
				
				$info = array();
				$info['name'] = (string)$entry->name;
				$info['size'] = (int)$entry->size;
				$info['kind'] = (string)$attribs['kind'];
				$info['path'] = (string)(empty($dir) ? $info['name'] : $dir.'/'.$info['name']);
				$info['commit'] = array(
					'author' => (string)$entry->commit->author,
					'date' => (string)$entry->commit->date,
					'rev' => (int)$entry->commit['revision']
					);
				if($info['kind'] == 'dir') $files['dirs'][] = $info;
				if($info['kind'] == 'file') $files['files'][] = $info;
			}
			
			return array_merge($files['dirs'],$files['files']);
		}
	}
	
	/**
	 * Get File
	 * Gets the contents of a file.
	 *
	 * @param string $file The filename.
	 * @return string
	 */
	public function getfile($file)
	{
		$descriptorspec = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
		$process = proc_open("svn cat ".$this->location.$file.$this->userpass(), $descriptorspec, $pipes);
		if(is_resource($process))
		{
			fclose($pipes[0]);
			return stream_get_contents($pipes[1]);
		}
	}
	
	/**
	 * Get Info
	 * Fetches information about the path.
	 *
	 * @param string $path The path
	 * @return array
	 */
	public function getinfo($path)
	{
		$descriptorspec = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
		$process = proc_open("svn info --xml ".$this->location.$path.$this->userpass(), $descriptorspec, $pipes);
		if(is_resource($process))
		{
			fclose($pipes[0]);
			$contents = stream_get_contents($pipes[1]);
			
			$xml = new SimpleXMLElement($contents);
			$ext = strtolower(strrchr($path,'.'));
			
			$info = array(
				'kind' => (string)$xml->entry['kind'],
				'name' => (string)$xml->entry['path'],
				'mime-type' => @$this->mimetypes[$ext]
			);
			
			return $info;
		}
	}
	
	// Used to clean the directory name.
	private function cleandirname($dir)
	{
		if(empty($dir)) return;
		return (substr($dir,-1) == '/' ? $dir : $dir.'/');
	}
}
