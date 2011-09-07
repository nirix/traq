<?php
/**
 * Traq 2
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

// Set the full path to the Traq folder
define('TRAQPATH',str_replace(pathinfo(__FILE__,PATHINFO_BASENAME),'',__FILE__));

// Fetch core file.
require('system/global.php');

if(isset($conf['general']['authorized_only'])
&& $conf['general']['authorized_only'] == true
&& !$user->loggedin && @$_POST['action'] != 'login'
&& ($uri->seg[0] != 'user' && $uri->seg[1] != 'register')) {
    include(template('user/login'));
    exit;
}

// Project listing
if(empty($uri->seg[0]) or $uri->seg[0] == 'index.php')
{
	require('system/controllers/projectlist.php');
}
// User pages
elseif($uri->seg[0] == 'user')
{
	require('system/controllers/user.php');
}
// Ajax page
elseif($uri->seg[0] == '_ajax')
{
	require('system/controllers/ajax.php');
}
// Project pages
elseif(is_project($uri->seg[0]))
{
	require('system/controllers/project.php');
}
// Something we're not sure of... load the 404 page...
else
{
	include(template('404'));
}
