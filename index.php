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

// Gets the path to the Traq installation.
define('TRAQPATH',str_replace(pathinfo(__FILE__,PATHINFO_BASENAME),'',__FILE__));

// Get the core file
require(TRAQPATH."include/global.php");

($hook = FishHook::hook('index_start')) ? eval($hook) : false;

// Lets load the right file for this page
if(empty($uri->seg[0])) {
	// Project Listing Page
	include("handlers/projectlisting.php");
} elseif($uri->seg[0] == "user") {
	// User Pages
	include("handlers/user.php");
} elseif(is_project($uri->seg[0])) {
	// Project pages
	include("handlers/project.php");
}

($hook = FishHook::hook('index_end')) ? eval($hook) : false;
?>