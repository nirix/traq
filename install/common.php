<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
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
 *
 * $Id$
 */

/**
* Settings
* Used to get the value of the specified setting.
* @param string $setting The setting...
*/
function settings($setting)
{
	global $db;
	$result = $db->fetcharray($db->query("SELECT setting, value FROM ".DBPF."settings WHERE setting='".$db->res($setting)."' LIMIT 1"));
	return $result['value'];
}

/**
 * Error
 * Used to display an error message.
 * @param string $title Error title.
 * @param string $message Error message.
 */
function error($title,$message)
{
	die("<blockquote style=\"border:2px solid darkred;padding:5px;background:#f9f9f9;font-family:arial; font-size: 14px;\"><h1 style=\"margin:0px;color:#000;border-bottom:1px solid #000;margin-bottom:10px;\">".$title." Error</h1><div style=\"padding: 0;\">".$message."</div><div style=\"color:#999;border-top:1px solid #000;margin-top:10px;font-size:small;padding-top:2px;\">Traq ".TRAQVER." &copy; 2009 Jack Polgar</div></blockquote>");
}

// Install/Upgrade header.
function head($script) {
	if($script == 'install') $title = 'Install';
	if($script == 'upgrade') $title = 'Upgrade';
	if($script == 'migrate') $title = 'Migrate';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Traq <?php echo $title?></title>
<style type="text/css">
body {
	background: #e8f5fd;
	color: #000;
	font-family: arial;
	font-size: 14px;
	margin: 0;
	padding: 0;
}
#wrapper {
	width: 600px;
	margin: 0 auto;
}
#page {
	background: #fff;
	padding: 10px;
	border-left: 1px solid #95d2f6;
	border-right: 1px solid #95d2f6;
}
a {
	color: #0092e8;
}
#head {
	padding: 5px;
	border-bottom: 1px solid #95d2f6;
}
#head h1 {
	margin: 0;
}
#foot {
	border-top: 1px solid #95d2f6;
	padding: 5px;
	font-size: small;
}

h2 {
	margin: 0;
}

.good {
	
	color: #317b2c;
	
}

.bad {
	color: #7b181c;
}
</style>
</head>
<body>
<div id="wrapper">
	<div id="head">
		<h1>Traq <?php echo (isset($title) ? $title :'')?></h1>
	</div>
	<div id="page">
<?php
}

// Install/Upgrade footer.
function foot() {
?>
	</div>
	<div id="foot">
		Traq <?php echo TRAQVER?>,<br />
		Copyright &copy; <?php echo date("Y"); ?> Jack Polgar
	</div>
</div>
</body>
</html>
<?php
}

class FishHook
{
	public static function hook($hook) {}
}
?>