<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Fetch Origin
require(TRAQPATH."origin/origin.php");
$origin = new Origin;
$origin->load("template");
$origin->template->templatedir = TRAQPATH.'/templates/';

// Fetch common functions file
require("common.php");
?>