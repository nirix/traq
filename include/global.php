<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

session_start();

// Fetch Version file
include(TRAQPATH."include/version.php");

// Fetch Config
require(TRAQPATH."include/config.php");

// Fetch Origin
require(TRAQPATH."include/origin/origin.php");
$origin = new Origin;
$db =& $origin->load("database",'db');
$origin->db->prefix = $config->db->prefix;
define("DBPREFIX",$origin->db->prefix);
$origin->db->connect($config->db->host,$config->db->user,$config->db->pass);
$origin->db->selectdb($config->db->name);
$origin->load("template");
$user =& $origin->load("user");
$uri =& $origin->load("uri");
$origin->load("bbcode");

// Load FishHook and Plugins
require(TRAQPATH."include/fishhook.php");
$fetchplugins = $db->query("SELECT * FROM ".DBPREFIX."plugins");
while($info = $db->fetcharray($fetchplugins)) {
	include(TRAQPATH.'plugins/'.$info['file']);
	FishHook::hook('plugins_load');
}
unset($fetchplugins,$info);

// Fetch common functions file
require(TRAQPATH."include/common.php");

// Get settings
$settings = (object) array();
$fetchsettings = $origin->db->query("SELECT setting,value FROM ".DBPREFIX."settings");
while($info = $origin->db->fetcharray($fetchsettings)) {
	$settings->$info['setting'] = $info['value'];
	FishHook::Hook("settings_fetch");
}
unset($fetchsettings,$info);
$origin->template->templatedir = TRAQPATH.'/templates/'.$settings->theme.'/';
FishHook::hook("global_end");
?>