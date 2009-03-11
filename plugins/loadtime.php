<?php
/**
 * Plugin Info
 * Author: Rainbird Studios
 * Name: Load Time
 * Info: Displays the load time of each page.
 * Version: 1.0
 * Copyright (c) 2009 Rainbird Studios
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved.
 */

// Load Time Start function
function loadtime_start() {
	global $loadtime;
	$loadtime['start'] = microtime();
}
FishHook::add('loadtime_start','global_end'); // Add to FishHook

// Load Time End
function loadtime_end() {
	global $loadtime;
	$loadtime['finish'] = microtime();
	list($sm, $ss) = explode(' ', $loadtime['start']);
	list($em, $es) = explode(' ', $loadtime['finish']);
	echo "<center>Page loaded in ".number_format(($em + $es) - ($sm + $ss), 3)." seconds.</center>";
}
FishHook::add('loadtime_end','template_footer'); // Add to FishHook
?>