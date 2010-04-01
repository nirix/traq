<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Get attachment info
$attachment = $db->queryfirst("SELECT name,contents,type,size FROM ".DBPF."attachments WHERE id='".$db->res($matches['id'])."' LIMIT 1");
$type = explode('/',$attachment['type']);

header("Content-type: ".$attachment['type']); // Set the page content-type

if($type[0] == 'text' or $type[0] == 'image')
{
	header("Content-Disposition: filename=\"".$attachment['name']."\""); // Set the content disposition and filename
}
else
{
	header("Content-Disposition: attachment; filename=\"".$attachment['name']."\""); // Set the content disposition and filename
}

($hook = FishHook::hook('attachment_view')) ? eval($hook) : false;

print(base64_decode($attachment['contents'])); // Print the attachment contents
?>