<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Fetch Config
require(TRAQPATH."include/config.php");

// Fetch Origin
require(TRAQPATH."origin/origin.php");
$origin = new Origin;
$origin->load("database",'db');
$origin->db->prefix = "t_";
$origin->db->connect($config->db->host,$config->db->user,$config->db->pass);
$origin->db->selectdb($config->db->name);
$origin->load("template");
$origin->template->templatedir = TRAQPATH.'/templates/';
$origin->load("user");

// Fetch common functions file
require("common.php");
?>