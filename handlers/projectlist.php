<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

$projects = array();
$fetch = $db->query("SELECT * FROM ".DBPF."projects ORDER BY displayorder ASC");
while($info = $db->fetcharray($fetch))
{
	($hook = FishHook::hook('projectlist_fetch')) ? eval($hook) : false;
	$projects[] = $info;
}

($hook = FishHook::hook('handler_projectlist')) ? eval($hook) : false;

require(template('projectlist'));
?>