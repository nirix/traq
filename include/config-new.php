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