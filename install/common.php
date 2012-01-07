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

// Strip magic quotes from request data.
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
{
	$quotes_sybase = strtolower(ini_get('magic_quotes_sybase'));
	$unescape_function = (empty($quotes_sybase) || $quotes_sybase === 'off') ? 'stripslashes($value)' : 'str_replace("\'\'","\'",$value)';
	$stripslashes_deep = create_function('&$value, $fn', '
		if (is_string($value)) {
			$value = ' . $unescape_function . ';
		} else if (is_array($value)) {
			foreach ($value as &$v) $fn($v, $fn);
		}
	');
	
	// Unescape data
	$stripslashes_deep($_POST, $stripslashes_deep);
	$stripslashes_deep($_GET, $stripslashes_deep);
	$stripslashes_deep($_COOKIE, $stripslashes_deep);
	$stripslashes_deep($_REQUEST, $stripslashes_deep);
} 

/**
 * Checks if Traq is already installed.
 */
function is_installed()
{
	$installed = false;
	if(file_exists("../system/config.php"))
	{
		require_once "../system/config.php";
		$link = mysql_connect($conf['db']['server'], $conf['db']['user'], $conf['db']['pass']);
		mysql_select_db($conf['db']['dbname'], $link);
		
		$tableCheck = mysql_query("SHOW TABLES", $link);
		while($info = mysql_fetch_array($tableCheck))
		{
			if($info[0] == $conf['db']['prefix'].'settings')
			{
				$installed = true;
				break;
			}
		}
	}
	
	return $installed;
}

/**
 * Prints the Install and Upgrade header
 * @param string $type Page type (install or upgrade)
 * @param integer $step The step the install/upgrade is on (1, 2, 3, etc)
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

/**
 * Prints the Install and Upgrade footer
 */
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

/**
 * Displays an error message
 * @param string $title The error title
 * @param string $message The error message
 */
function error($title, $message)
{
	echo '<div class="message error">'.$title.' error: '.$message.'</div>';
	exit;
}

function aselect($index, $array) {	return $array[$index]; }