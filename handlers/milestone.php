<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
 *
 * This file is part of Traq.
 * 
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
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

// Get the milestone
$milestone = $db->query("SELECT * FROM ".DBPF."milestones WHERE slug='".$db->es($matches['slug'])."' AND project_id='".$project['id']."' LIMIT 1");

// Check the milestone exists...
if(!$db->numrows($milestone)) die();

// Fetch milestone info
$milestone = $db->fetcharray($milestone);

// Get the milestone tickets
$milestone['tickets'] = array();
$milestone['tickets']['open'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$milestone['id']."' AND project_id='".$project['id']."' AND closed='0'"));
$milestone['tickets']['closed'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$milestone['id']."' AND project_id='".$project['id']."' AND closed='1'"));
$milestone['tickets']['total'] = ($milestone['tickets']['open']+$milestone['tickets']['closed']);
$milestone['tickets']['percent'] = array(
	'open' => ($milestone['tickets']['open'] ? getpercent($milestone['tickets']['open'],$milestone['tickets']['total']) : 0),
	'closed' => getpercent($milestone['tickets']['closed'],$milestone['tickets']['total'])
);

addcrumb($uri->anchor($project['slug'],'roadmap'),l('roadmap'));
addcrumb($uri->geturi(),l('milestone').': '.$milestone['milestone']);

($hook = FishHook::hook('handler_milestone')) ? eval($hook) : false;

require(template('milestone'));
?>