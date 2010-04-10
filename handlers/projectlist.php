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