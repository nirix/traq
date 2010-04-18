<?php
/**
 * Traq 2
 * Copyright (c) 2009, 2010 Jack Polgar
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

// Define a few things...
$CACHE = array('settings'=>array());
$breadcrumbs = array();

// Traq Version
require('version.php');

// Fetch core files.
require(TRAQPATH.'inc/db.class.php');
require(TRAQPATH.'inc/user.class.php');
require(TRAQPATH.'inc/fishhook.class.php');
require(TRAQPATH.'inc/uri.class.php');
require(TRAQPATH.'inc/common.php');
require(TRAQPATH.'inc/config.php');

// Start the DB class.
$db = new Database($conf['db']['server'],$conf['db']['user'],$conf['db']['pass'],$conf['db']['dbname']);
define("DBPF",$conf['db']['prefix']);

// Start the other required class
$user = new User;
$uri = new URI;

// Define the THEMEDIR
define("THEMEDIR",$uri->anchor('templates',settings('theme')));

// Set the SEO URL option
$uri->style = settings('seo_urls');

// Fetch locale file...
require('locale/'.settings('locale'));

($hook = FishHook::hook('global')) ? eval($hook) : false;
?>