<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Get the projects to list
$projects = array();
$fetchprojects = $db->query("SELECT * FROM ".DBPREFIX."projects ORDER BY name ASC");
while($info = $db->fetcharray($fetchprojects)) {
	// Get Tickets
	$info['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND projectid='".$info['id']."'"));
	$info['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status='0' AND projectid='".$info['id']."'"));
	$info['desc'] = $wikiformat->format($info['desc']);
	$projects[] = $info;
}
unset($fetchprojects,$info);

// Load the Project Listing template
include(template('projectlisting'));
?>