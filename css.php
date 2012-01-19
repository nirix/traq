<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
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

header("content-type: text/css; charset: UTF-8;");

if (extension_loaded('zlib'))
{
	ob_start('ob_gzhandler');
}

if (!isset($_REQUEST['css']))
{
	exit;
}

require "./system/avalon/libs/request.php";

$output = array();
foreach (explode(',', $_REQUEST['css']) as $file)
{
	$css = file_get_contents("./assets/css/{$file}.css");
	$output[] = str_replace(':baseurl:', Request::base(), $css);
}

$output = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $output);
echo str_replace(array("\n\r", "\n", "\r", "\t", '  ', '   ', '    '), '', implode('', $output));