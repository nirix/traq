<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Gets the path to the Traq installation.
define('TRAQPATH',str_replace(pathinfo(__FILE__,PATHINFO_BASENAME),'',__FILE__));

// Get the core file
require(TRAQPATH."include/global.php");

// Lets load the right file for this page
if(empty($uri->seg[0])) {
	include("handlers/projectlisting.php");
} elseif($uri->seg[0] == "user") {
	include("handlers/user.php");
} elseif(is_project($uri->seg[0])) {
	include("handlers/project.php");
}
?>