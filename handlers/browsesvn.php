<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

if(empty($project['sourcelocation'])) {
	exit;
}

$breadcrumbs[$uri->anchor($project['slug'],'source')] = "Browse Source";
include(TRAQPATH.'include/svn.class.php');
$svn = new Traq_Subversion;
$svn->setrepo($project['sourcelocation']);
$svn->prefix = $project['id'];

$count = 0;
$svnloc = '';
foreach(array_slice($uri->seg, 2) as $svndir) {
	$svndirloc .= str_replace('/','',$svndir).'/';
	$breadcrumbs[$uri->anchor($project['slug'],'source').$svndirloc] = $svndir;
	$count++;
}

$dir = implode('/',array_slice($uri->seg, 2));

$info = $svn->info($dir);
if(file_exists(TRAQPATH.'svncache/'.$project['id'].'-'.$info['rev'].(empty($dir) ? 'root' : str_replace('/','-',$dir)).'.txt')) {
	$file = file_get_contents(TRAQPATH.'svncache/'.$project['id'].'-'.$info['rev'].(empty($dir) ? 'root' : str_replace('/','-',$dir)).'.txt');
	$list = unserialize($file);
} else {
	$list = $svn->listdir($dir);
}
include(template('browsesvn'));
?>