<?php
/**
 * Traq
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

// Start the session...
session_start();

// Fetch Version file
include(TRAQPATH."include/version.php");

// Fetch Config
require(TRAQPATH."include/config.php");

// Fetch Origin
require(TRAQPATH."include/origin/origin.php");
$origin = new Origin;

// Load DB Class
$db =& $origin->load("database",'db');
$origin->db->prefix = $config->db->prefix;
define("DBPREFIX",$origin->db->prefix);

// Connect to the Database
$origin->db->connect($config->db->host,$config->db->user,$config->db->pass);
$origin->db->selectdb($config->db->name);

// Load Template Class
$origin->load("template");

// Load User Class
$user =& $origin->load("user");

// Load URI Class
$uri =& $origin->load("uri");

// Load BBCode Class
$origin->load("bbcode");

// Load FishHook and Plugins
require(TRAQPATH."include/fishhook.php");
$fetchplugins = $db->query("SELECT * FROM ".DBPREFIX."plugins");
while($info = $db->fetcharray($fetchplugins))
{
	// Check if the plugin file exists, if so include it
	if(file_exists(TRAQPATH.'plugins/'.$info['file']))
	{
		include(TRAQPATH.'plugins/'.$info['file']); // Include the plugin file
	}
}
unset($fetchplugins,$info);

// Set Content type and charset
header("Content-Type: text/html; charset=UTF-8");

// Fetch common functions file
require(TRAQPATH."include/common.php");

// Get settings
$settings = (object) array();
$fetchsettings = $origin->db->query("SELECT setting,value FROM ".DBPREFIX."settings");
while($info = $origin->db->fetcharray($fetchsettings))
{
	$settings->$info['setting'] = $info['value'];
	FishHook::Hook("settings_fetch"); // Plugin hook
}
unset($fetchsettings,$info);

// Set template directory
$origin->template->templatedir = TRAQPATH.'/templates/'.$settings->theme.'/';

// Set the URI type
$uri->type = $settings->uritype;

// Load the language
require(TRAQPATH."include/lang/enus.php");

FishHook::hook("global_end"); // Plugin hook
?>