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

// Check if the repository url segment is set,
// if not redirect to the main repository.
if(!isset($uri->seg[2]))
{
	$repo = $db->queryfirst("SELECT slug FROM ".DBPF."repositories WHERE main='1' AND project_id='".$project['id']."' LIMIT 1");
	header("Location: ".$uri->anchor($project['slug'],'source',$repo['slug']));
}

// Get the repository info
$repo = $db->queryfirst("SELECT * FROM ".DBPF."repositories WHERE slug='".$db->res($uri->seg[2])."' AND project_id='".$project['id']."' LIMIT 1");
$repo['type'] = str_replace('.class','',$repo['file']);

// Check the repository exists...
if(empty($repo['name'])) exit;

// Build breadcrumbs
addcrumb($uri->anchor($project['slug'],'source'),l('source'));
addcrumb($uri->anchor($project['slug'],'source',$repo['slug']),$repo['name']);

foreach(array_slice($uri->seg,3) as $dir)
{
	static $dirs = array();
	$dirs[] = $dir;
	addcrumb($uri->anchor($project['slug'],'source',$repo['slug'],implode('/',$dirs)),$dir);
}

// Fetch repository browser files..
require(TRAQPATH.'inc/source.class.php');
require(TRAQPATH.'inc/'.$repo['file'].'.php');

// Initiate the browser and fetch the file list..
$source = new $repo['type']($repo['location']);
$files = $source->ls(implode('/',array_slice($uri->seg,3)));

include(template('source'));
?>