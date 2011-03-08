<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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

function head($type, $step = false)
{
	$titles = array('install'=>'Install','upgrade'=>'Upgrade','migrate'=>'Migrate');
	$title = $titles[$type];
	
	if($step > 1)
	{
		$title .= " - Step ".$step;
	}
	
	echo '<!DOCTYPE html>'.PHP_EOL;
	echo '<html>'.PHP_EOL;
	echo '	<head>'.PHP_EOL;
	echo '		<title>Traq '.$title.'</title>'.PHP_EOL;
	echo '		<link href="install.css" media="screen" rel="stylesheet" type="text/css" />'.PHP_EOL;
	echo '	</head>'.PHP_EOL;
	echo '	<body>'.PHP_EOL;
	echo '		<div id="wrapper">'.PHP_EOL;
	echo '			<h1>Traq '.aselect(0, explode(' - ', $title)).'</h1>'.PHP_EOL;
	echo '			<div id="page">'.PHP_EOL;
}

function foot()
{
	echo '			</div>'.PHP_EOL;
	echo '			<div id="footer">'.PHP_EOL;
	echo '				Traq &copy; 2009-2011 Jack Polgar'.PHP_EOL;
	echo '			</div>'.PHP_EOL;
	echo '		</div>'.PHP_EOL;
	echo '	</body>'.PHP_EOL;
	echo '</html>'.PHP_EOL;
}

function error($title, $message)
{
	echo '<div class="message error">'.$title.' error: '.$message.'</div>';
	exit;
}

function aselect($index, $array) {	return $array[$index]; }