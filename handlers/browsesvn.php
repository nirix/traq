<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

// Check if the project source location is set, or exit
if(empty($project['sourcelocation'])) {
	exit;
}

// Breadcrumbs
$breadcrumbs[$uri->anchor($project['slug'],'source')] = "Browse Source";

// Fetch the Subversion class
include(TRAQPATH.'include/svn.class.php');
$svn = new Traq_Subversion;
$svn->setrepo($project['sourcelocation']); // Set the repo location
$svn->prefix = $project['id']; // Set the file prefix

// Create the breadcrumbs for the source directories
$svnloc = '';
foreach(array_slice($uri->seg, 2) as $svndir) {
	$svndirloc .= str_replace('/','',$svndir).'/';
	$breadcrumbs[$uri->anchor($project['slug'],'source').$svndirloc] = $svndir;
	$count++;
}

// Set the dir
$dir = implode('/',array_slice($uri->seg, 2));

// Get the dir info from svn
$info = $svn->info($dir);
if(file_exists(TRAQPATH.'svncache/'.$project['id'].'-'.$info['rev'].(empty($dir) ? 'root' : str_replace('/','-',$dir)).'.txt')) {
	$file = file_get_contents(TRAQPATH.'svncache/'.$project['id'].'-'.$info['rev'].(empty($dir) ? 'root' : str_replace('/','-',$dir)).'.txt');
	$list = unserialize($file);
} else {
	$list = $svn->listdir($dir);
}
include(template('browsesvn')); // Fetch the browsesvn template
?>