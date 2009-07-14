<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

define('TRAQPATH',str_replace(pathinfo(__FILE__,PATHINFO_BASENAME),'',__FILE__));

// Fetch core file.
require('inc/global.php');

// Project listing
if($uri->seg[0] == '' && !settings('single_project'))
{
	require('handlers/projectlist.php');
}
// User pages
elseif($uri->seg[0] == 'user')
{
	require('handlers/user.php');
}
// Project pages
elseif(is_project(PROJECT_SLUG))
{
	require('handlers/project.php');
}
// Something we're not sure of...
else
{
	error('Not Found','The page ('.$uri->geturi().') cannot be found.');
}
?>