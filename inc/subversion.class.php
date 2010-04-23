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
 *
 * $Id$
 */

class Subversion extends Source
{
	public $mimetypes = array(
		'.txt' => 'text/plain',
		'.htm' => 'text/plain',
		'.html' => 'text/plain',
		'.php' => 'text/plain',
		'.css' => 'text/css',
		'.js' => 'application/javascript',
		'.json' => 'application/json',
		'.xml' => 'application/xml',
		'.swf' => 'application/x-shockwave-flash',
		'.flv' => 'video/x-flv',
		'.png' => 'image/png',
		'.jpe' => 'image/jpeg',
		'.jpeg' => 'image/jpeg',
		'.jpg' => 'image/jpeg',
		'.gif' => 'image/gif',
		'.bmp' => 'image/bmp',
		'.ico' => 'image/vnd.microsoft.icon',
		'.tiff' => 'image/tiff',
		'.tif' => 'image/tiff',
		'.svg' => 'image/svg+xml',
		'.svgz' => 'image/svg+xml',
		'.zip' => 'multipart/x-zip',
		'.rar' => 'application/x-rar-compressed',
		'.exe' => 'application/x-msdownload',
		'.msi' => 'application/x-msdownload',
		'.cab' => 'application/vnd.ms-cab-compressed',
		'.mp3' => 'audio/x-mp3',
		'.qt' => 'video/quicktime',
		'.mov' => 'video/quicktime',
		'.pdf' => 'application/pdf',
		'.psd' => 'image/vnd.adobe.photoshop',
		'.ai' => 'application/postscript',
		'.eps' => 'application/postscript',
		'.ps' => 'application/postscript',
		'.doc' => 'application/msword',
		'.rtf' => 'application/rtf',
		'.xls' => 'application/vnd.ms-excel',
		'.ppt' => 'application/vnd.ms-powerpoint',
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'.dwg' => 'application/acad',
		'.arj' => 'application/arj',
		'.ccad' => 'application/clariscad',
		'.drw' => 'application/drafting',
		'.dxf' => 'application/dxf',
		'.xl' => 'application/excel',
		'.unv' => 'application/i-deas',
		'.igs' => 'application/iges',
		'.iges' => 'application/iges',
		'.hqx' => 'application/mac-binhex40',
		'.word' => 'application/msword',
		'.w6w' => 'application/msword',
		'.wri' => 'application/mswrite',
		'.bin' => 'application/octet-stream',
		'.oda' => 'application/oda',
		'.prt' => 'application/pro_eng',
		'.part' => 'application/pro_eng',
		'.set' => 'application/set',
		'.stl' => 'application/sla',
		'.sol' => 'application/solids',
		'.stp' => 'application/STEP',
		'.step' => 'application/STEP',
		'.vda' => 'application/vda',
		'.dir' => 'application/x-director',
		'.dcr' => 'application/x-director',
		'.dxr' => 'application/x-director',
		'.mif' => 'application/x-mif',
		'.csh' => 'application/x-csh',
		'.dvi' => 'application/x-dvi',
		'.gz' => 'application/x-gzip',
		'.gzip' => 'multipart/x-gzip',
		'.hdf' => 'application/x-hdf',
		'.latex' => 'application/x-latex',
		'.nc' => 'application/x-netcdf',
		'.cdf' => 'application/x-netcdf',
		'.sit' => 'application/x-stuffit',
		'.tcl' => 'application/x-tcl',
		'.texinfo' => 'application/x-texinfo',
		'.texi' => 'application/x-texinfo',
		'.t' => 'application/x-troff',
		'.tr' => 'application/x-troff',
		'.roff' => 'application/x-troff',
		'.man' => 'application/x-troff-man',
		'.me' => 'application/x-troff-me',
		'.ms' => 'application/x-troff-ms',
		'.src' => 'application/x-wais-source',
		'.bcpio' => 'application/x-bcpio',
		'.cpio' => 'application/x-cpio',
		'.gtar' => 'application/x-gtar',
		'.shar' => 'application/x-shar',
		'.sv4cpio' => 'application/x-sv4cpio',
		'.sv4crc' => 'application/x-sv4crc',
		'.tar' => 'application/x-tar',
		'.ustar' => 'application/x-ustar',
		'.hlp' => 'application/x-winhelp',
		'.au' => 'audio/basic',
		'.snd' => 'audio/basic',
		'.aif' => 'audio/x-aiff',
		'.aiff' => 'audio/x-aiff',
		'.aifc' => 'audio/x-aiff',
		'.ra' => 'audio/x-pn-realaudio',
		'.ram' => 'audio/x-pn-realaudio',
		'.rpm' => 'audio/x-pn-realaudio-plugin',
		'.wav' => 'audio/x-wav',
		'.ief' => 'image/ief',
		'.pict' => 'image/pict',
		'.ras' => 'image/x-cmu-raster',
		'.pnm' => 'image/x-portable-anymap',
		'.pbm' => 'image/x-portable-bitmap',
		'.pgm' => 'image/x-portable-graymap',
		'.ppm' => 'image/x-portable-pixmap',
		'.rgb' => 'image/x-rgb',
		'.xbm' => 'image/x-xbitmap',
		'.xpm' => 'image/x-xpixmap',
		'.xwd' => 'image/x-xwindowdump',
		'.mpeg' => 'video/mpeg',
		'.mpg' => 'video/mpeg',
		'.mpe' => 'video/mpeg',
		'.avi' => 'video/msvideo',
		'.movie' => 'video/x-sgi-movie',
		'.wrl' => 'x-world/x-vrml',
		'.ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'.ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
		'.odp' => 'application/vnd.oasis.opendocument.presentation',
		'.otp' => 'application/vnd.oasis.opendocument.presentation-template',
		'.odg' => 'application/vnd.oasis.opendocument.graphics',
		'.otg' => 'application/vnd.oasis.opendocument.graphics-template',
		'.odc' => 'application/vnd.oasis.opendocument.chart',
		'.otc' => 'application/vnd.oasis.opendocument.chart-template',
		'.odf' => 'application/vnd.oasis.opendocument.formula',
		'.otf' => 'application/vnd.oasis.opendocument.formula-template',
		'.odi' => 'application/vnd.oasis.opendocument.image',
		'.oti' => 'application/vnd.oasis.opendocument.image-template',
		'.odb' => 'application/vnd.oasis.opendocument.database',
		'.odt' => 'application/vnd.oasis.opendocument.text',
		'.ott' => 'application/vnd.oasis.opendocument.text-template',
		'.odm' => 'application/vnd.oasis.opendocument.text-master',
		'.oth' => 'application/vnd.oasis.opendocument.text-web'
		);
	
	public function __construct($location)
	{
		if(substr($location,0,1) == '/')
			$this->location = substr($location,1);
		else
			$this->location = $location.'/';
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
		$info = exec("svn ls --xml ".$this->location.$dir,$_a,$_r);
		
		// Loop through the entries
		$files = array('dirs'=>array(),'files'=>array());
		$xml = new SimpleXMLElement(implode('',$_a));
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
		$process = proc_open("svn cat ".$this->location.$file, $descriptorspec, $pipes);
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
	 * @param string $path The path/
	 * @return array
	 */
	public function getinfo($path)
	{
		$descriptorspec = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
		$process = proc_open("svn info --xml ".$this->location.$path, $descriptorspec, $pipes);
		if(is_resource($process))
		{
			fclose($pipes[0]);
			$contents = stream_get_contents($pipes[1]);
			
			$xml = new SimpleXMLElement($contents);
			$ext = strtolower(strrchr($path,'.'));
			
			$info = array(
				'kind' => (string)$xml->entry['kind'],
				'name' => (string)$xml->entry['path'],
				'mime-type' => $this->mimetypes[$ext]
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
?>