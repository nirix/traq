<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

/**
 * Traq Subversion Class
 * Copyright (c)2009 Jack Polgar
 */
class Traq_Subversion {
	private $repo = NULL; // Repository location
	
	/**
	 * File Prefix
	 * Placed at the start of the repository cache files.
	 * @var public
	 */
	public $prefix = NULL;
	
	/**
	 * Set Repository
	 * Used to set the repository location.
	 * @param string $repo Repository Path
	 */
	public function setrepo($repo) {
		if(substr($repo,-1) == '/') {
			$this->repo = $repo;
		} else {
			$this->repo = $repo.'/';
		}
	}
	
	/**
	 * Get Repository
	 * Used to get the repository location.
	 * @return string
	 */
	public function getrepo() {
		return $this->repo;
	}
	
	/**
	 * List Directory
	 * Used to list the specified subversion repository directory.
	 * @param string $dir Directory location
	 * @return array
	 */
	public function listdir($dir='') {
		$dir = $this->cleandirname($dir);
		$this->path = $dir;
		$descriptorspec = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
		$process = proc_open("svn list --xml ".$this->repo.$this->cleandirname($dir), $descriptorspec, $pipes); // Run the command
		if(is_resource($process)) {
			fclose($pipes[0]);
			$contents = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			$return_value = proc_close($process);
			$xml = new SimpleXMLElement($contents);
			$files = array();
			// Loop through the entries
			foreach($xml->list->entry as $entry) {
				$attribs = $entry->attributes();
				$commitattribs = $entry->commit->attributes();
				// Set the file/dir info array.
				$info = array();
				$info['name'] = (string)$entry->name;
				$info['size'] = (int)$entry->size;
				$info['kind'] = (string)$attribs['kind'];
				$info['path'] = (string)(empty($dir) ? $info['name'] : $dir.'/'.$info['name']);
				$info['commit'] = array('author'=>(string)$entry->commit->author,'date'=>(string)$entry->commit->date,'rev'=>(int)$commitattribs->revision);
				$files[] = $info;
			}
			// Cache the directory if it doesnt exist already.
			if(!file_exists(TRAQPATH.'svncache/'.$this->prefix.'-'.$this->info['rev'].str_replace('/','-',(empty($dir) ? 'root' : $dir)).'.txt')) {
				$fp = fopen(TRAQPATH.'svncache/'.$this->prefix.'-'.$this->info['rev'].str_replace('/','-',(empty($dir) ? 'root' : $dir)).'.txt', 'w');
				fwrite($fp, serialize($files));
				fclose($fp);
			}
			return $files;
		}
	}
	
	/**
	 * Get Info
	 * Used to get the information of the specified subversion repository directory.
	 * @param string $dir Directory location
	 * @return array
	 */
	public function info($dir='') {
		$this->path = $dir;
		$descriptorspec = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
		$process = proc_open("svn info --xml ".$this->repo.$this->cleandirname($dir), $descriptorspec, $pipes); // Run the command
		if(is_resource($process)) {
			fclose($pipes[0]);
			$contents = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			$return_value = proc_close($process);
			$xml = new SimpleXMLElement($contents);
			$attribs = $xml->entry->commit->attributes();
			$info['rev'] = (int)$attribs->revision;
			$this->info = $info;
			return $info;
		}
	}
	
	// Used to clean the directory name.
	private function cleandirname($dir) {
		if(substr($dir,0,1) == '/') {
			$dir = substr($dir,1);
		}
		return $dir;
	}
}
?>