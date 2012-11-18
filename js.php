<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

// Set content type and charset.
header("content-type: text/javascript; charset=UTF-8");

// Check if we can gzip the page or not/
if (extension_loaded('zlib')) {
    // We can!
    ob_end_clean();
    ob_start('ob_gzhandler');
}

// Make sure there are files to load.
if (!isset($_REQUEST['js'])) {
    exit;
}

$output = array();

if ($_REQUEST['js'] == 'all') {
    foreach (scandir(__DIR__ . '/assets/js') as $file) {
        if (substr($file, -3) == '.js') {
            $output[] = file_get_contents(__DIR__ . "/assets/js/{$file}");
        }
    }
} else {
    foreach (explode(',', $_REQUEST['js']) as $file) {
        // Make sure the file exists...
        if (file_exists(__DIR__ . "/assets/js/{$file}.js")) {
            // Add it to the output array.
            $output[] = file_get_contents(__DIR__ . "/assets/js/{$file}.js");
        }
    }
}

// Display all the files.
echo implode("\n/* -------------------- */\n", $output);
