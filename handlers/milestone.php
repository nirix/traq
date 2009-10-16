<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

$milestone = $db->queryfirst("SELECT * FROM ".DBPF."milestones WHERE slug='".$db->es($matches['slug'])."' AND project_id='".$project['id']."' LIMIT 1");
$milestone['tickets'] = array();
$milestone['tickets']['open'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$milestone['id']."' AND project_id='".$project['id']."' AND closed='0' LIMIT 1"));
$milestone['tickets']['closed'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$milestone['id']."' AND project_id='".$project['id']."' AND closed='1' LIMIT 1"));
$milestone['tickets']['total'] = ($milestone['tickets']['open']+$milestone['tickets']['closed']);
$milestone['tickets']['percent'] = array(
	'open' => ($milestone['tickets']['open'] ? getpercent($milestone['tickets']['open'],$milestone['tickets']['total']) : 0),
	'closed' => getpercent($milestone['tickets']['closed'],$milestone['tickets']['total'])
);

addcrumb($uri->anchor($project['slug'],'roadmap'),l('roadmap'));
addcrumb($uri->geturi(),l('milestone').': '.$milestone['milestone']);

require(template('milestone'));
?>