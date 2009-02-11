<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Fetch Version file
include(TRAQPATH."include/version.php");

// Fetch Config
require(TRAQPATH."include/config.php");

// Fetch Origin
require(TRAQPATH."include/origin/origin.php");
$origin = new Origin;
$origin->load("database",'db');
$origin->db->prefix = $config->db->prefix;
define("DBPREFIX",$origin->db->prefix);
$origin->db->connect($config->db->host,$config->db->user,$config->db->pass);
$origin->db->selectdb($config->db->name);
$origin->load("template");
$origin->load("user");
$origin->load("uri");
$origin->load("wikiformat");
$db =& $origin->db;
$user =& $origin->user;
$uri =& $origin->uri;
$wikimarkup =& $origin->wikiformat;

// Fetch common functions file
require(TRAQPATH."include/common.php");

// Get settings
$settings = (object) array();
$fetchsettings = $origin->db->query("SELECT setting,value FROM ".DBPREFIX."settings");
while($info = $origin->db->fetcharray($fetchsettings)) {
	$settings->$info['setting'] = $info['value'];
}
unset($fetchsettings,$info);
$origin->template->templatedir = TRAQPATH.'/templates/'.$settings->theme.'/';
?>