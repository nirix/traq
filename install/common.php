<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

function head($title) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Traq <?=$title?></title>
<style type="text/css">
body {
	margin: 10px;
	padding: 0px;
	color: #000;
	background: #fff;
	font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
}
a {
	color: #000;
	text-decoration: underline;
}
a:hover {
	color: #000;
	text-decoration: none;
}
#wrapper {
	width: 900px;
	margin: 0 auto;
}
#header span {
	font-size: 25px;
	font-weight: bold;
}
#header span a {
	text-decoration: none;
}
#page {
	border: 1px solid #d7d7d7;
	border-left: 0px;
	border-right: 0px;
	padding: 10px;
	margin: 5px 0;
}
#buttons {
	text-align: center;
}
input, textarea, select {
	margin: 2px;
}
input, select {
	vertical-align: middle;
}
input[type=button], input[type=submit], input[type=reset] {
	background: #eee;
	color: #222;
	border: 1px outset #ccc;
	padding: 2px 6px;
}
input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover {
	background: #fff;
}
input[type=button][disabled], input[type=submit][disabled],
input[type=reset][disabled] {
	background: #f6f6f6;
	border-style: solid;
	color: #999;
}
input[type=text], input[type=password], textarea {
	border: 1px solid #d7d7d7;
}
input[type=text], input[type=password] {
	padding: 4px 4px;
}
input[type=text]:focus, input[type=password]:focus, textarea:focus {
	border: 1px solid #886;
}
textarea.big {
	width: 100%;
	height: 250px;
}
fieldset {
	border: 1px solid #d7d7d7;
	padding: 4px;
	margin: 0;
}

</style>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<span>Traq <?=$title?></span>
	</div>
	<div id="page">
<?
}

function foot() {
?>
	</div>
	<div id="footer">
		Traq,<br />
		Copyright &copy;2009 Rainbird Studios
	</div>
</div>
</body>
</html>
<?
}
?>