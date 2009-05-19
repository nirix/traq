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

// Fetch required classes
require(TRAQPATH."include/database.class.php");
require(TRAQPATH."include/user.class.php");
require(TRAQPATH."include/uri.class.php");
require(TRAQPATH."include/bbcode.class.php");

// Load DB Class
$db = new Database;
$db->prefix = $config->db->prefix;
define("DBPREFIX",$db->prefix);

// Connect to the Database
$db->connect($config->db->host,$config->db->user,$config->db->pass);
$db->selectdb($config->db->name);

// Load User Class
$user = new User;

// Load URI Class
$uri = new URI;

// Load BBCode Class
$bbcode = new BBCode;

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
$fetchsettings = $db->query("SELECT setting,value FROM ".DBPREFIX."settings");
while($info = $db->fetcharray($fetchsettings))
{
	$settings->$info['setting'] = $info['value'];
	($hook = FishHook::hook('settings_fetch')) ? eval($hook) : false;
}
unset($fetchsettings,$info);

// Set the URI type
$uri->type = $settings->uritype;

// Load the language
require(TRAQPATH."include/lang/enus.php");

// Load Akismet class
require(TRAQPATH."include/akismet.class.php");
if($settings->akismetkey != '')
{
	$akismet = new Akismet(($_SERVER['HTTPS'] != '' ? 'http://' : 'http://').$_SERVER['HTTP_HOST'], $settings->akismetkey);
}

($hook = FishHook::hook('global_end')) ? eval($hook) : false;
?>