<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// TRAQ Config
$config = (object) array('db');

// TRAQ Database Config
$config->db = (object) array(
							 'host' => 'localhost',	// DB Server
							 'user' => 'root',	// DB Username
							 'pass' => 'root',	// DB Password
							 'name' => 'traq',	// DB Name
							 'prefix' => 't_'	// DB Table Prefix
							 );
?>