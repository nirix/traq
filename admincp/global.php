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
define('TRAQPATH',str_replace(pathinfo('../index.php',PATHINFO_BASENAME),'','../index.php'));

// Get the core file
require(TRAQPATH."include/global.php");

require(TRAQPATH."admincp/common.php");
?>