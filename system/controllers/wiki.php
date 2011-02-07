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

addcrumb($uri->anchor($project['slug'],'wiki'),l('Wiki'));

// Main Wiki page
if(!isset($uri->seg[2]))
	$wiki = $db->queryfirst("SELECT * FROM ".DBPF."wiki WHERE project_id='".$project['id']."' AND main='1' LIMIT 1");
// Specific Wiki page
else
{
	$wiki = $db->queryfirst("SELECT * FROM ".DBPF."wiki WHERE project_id='".$project['id']."' AND slug='".$db->res($uri->seg[2])."' LIMIT 1");
	addcrumb($uri->anchor($project['slug'],'wiki',$wiki['slug']),$wiki['title']);
}

($hook = FishHook::hook('handler_wiki')) ? eval($hook) : false;

require(template("wiki"));
?>