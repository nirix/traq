<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Gets the path to the Traq installation.
define('TRAQPATH',str_replace(pathinfo('../index.php',PATHINFO_BASENAME),'','../index.php'));

// Get the core file
require(TRAQPATH."include/global.php");

require(TRAQPATH."admincp/common.php");
?>