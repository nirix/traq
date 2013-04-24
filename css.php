<?php
/*
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

// Set the content type and charset.
header("content-type: text/css; charset: UTF-8;");

// Check if we can gzip the page or not/
if (extension_loaded('zlib')) {
    // We can!
    ob_end_clean();
    ob_start('ob_gzhandler');
}

// Check for the CSS index in the request array..
if (!isset($_REQUEST['css']) and !isset($_REQUEST['theme'])) {
    exit;
}

// Fetch the request class.
require "./vendor/avalon/http/request.php";
use avalon\http\Request;
Request::init();

$output = array();

// assets/css files
if (isset($_REQUEST['css'])) {
    foreach (explode(',', $_REQUEST['css']) as $file) {
        // Check if the file exists...
        if (file_exists(__DIR__ . "/assets/css/{$file}.css")) {
            // Add it to the output array.
            $output[] = file_get_contents(__DIR__ . "/assets/css/{$file}.css");
        }
    }
}

// Set `theme_files` to `default` if it's
// not set in the URI.
if (!isset($_REQUEST['theme_files'])) {
    $_REQUEST['theme_files'] = 'default';
}

// Theme CSS files
if (isset($_REQUEST['theme'])) {
    $theme = htmlspecialchars($_REQUEST['theme']);
    foreach (explode(',', $_REQUEST['theme_files']) as $file) {
        // Check if the file exists...
        if (file_exists(__DIR__ . "/vendor/traq/views/{$theme}/css/{$file}.css")) {
            // Add it to the output array.
            $output[] = file_get_contents(__DIR__ . "/vendor/traq/views/{$theme}/css/{$file}.css");
        }
    }
}

$output = implode('', $output);

// Replace the :baseuri: token
$output = str_replace(':baseuri:', Request::base(), $output);

// Remove comments and such from the output.
$output = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $output);
$output = preg_replace('/\s*(,|;|:|{|})\s*/', '$1', $output);

// Minify the CSS.
echo str_replace(array("\t", "\n"), '', $output);
