<?php
/**
 * Traq 2
 * Copyright (c) 2009
 * All Rights Reserved
 *
 * $Id: index.php 2010-04-07 19:28:16Z pierre $
 */

// what step are we currently on?
$step = (isset($_POST['step']) && !empty($_POST['step'])) ? intval($_POST['step']) : 1;

$next_step = $step + 1;

// Fetch required files
require('common.php');
require('../system/version.php');
//require('../inc/fishhook.class.php');

head('install');

require_once('step'.$step.'.php');

foot();
?>
