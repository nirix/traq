<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
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

// Get attachment info
$attachment = $db->queryfirst("SELECT name,contents,type,size FROM ".DBPF."attachments WHERE id='".$db->res($matches['id'])."' LIMIT 1");
$type = explode('/',$attachment['type']);

header("Content-type: ".$attachment['type']); // Set the page content-type

// Check what type of file we're dealing with.
if($type[0] == 'text' or $type[0] == 'image')
	header("Content-Disposition: filename=\"".$attachment['name']."\""); // Set the content disposition and filename
else
	header("Content-Disposition: attachment; filename=\"".$attachment['name']."\""); // Set the content disposition and filename

($hook = FishHook::hook('attachment_view')) ? eval($hook) : false;

print(base64_decode($attachment['contents'])); // Print the attachment contents
?>