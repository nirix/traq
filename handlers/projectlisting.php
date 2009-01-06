<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Get the projects to list
$fetchprojects = $db->query("SELECT * FROM ".DBPREFIX."projects ORDER BY name ASC");
while($info = $db->fetcharray($fetchprojects)) {
	$projects[] = $info;
}
unset($fetchprojects,$info);

// Load the Project Listing template
include(template('projectlisting'));
?>