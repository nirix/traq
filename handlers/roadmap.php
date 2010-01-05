<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Fetch project milestones...
$milestones = array();
$fetch = $db->query("SELECT * FROM ".DBPF."milestones WHERE project_id='".$db->es($project['id'])."' AND locked='0' ORDER BY displayorder ASC");
while($info = $db->fetcharray($fetch))
{
	$info['tickets'] = array();
	$info['tickets']['open'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$info['id']."' AND project_id='".$project['id']."' AND closed='0'"));
	$info['tickets']['closed'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$info['id']."' AND project_id='".$project['id']."' AND closed='1'"));
	$info['tickets']['total'] = ($info['tickets']['open']+$info['tickets']['closed']);
	$info['tickets']['percent'] = array(
		'open' => ($info['tickets']['open'] ? getpercent($info['tickets']['open'],$info['tickets']['total']) : 0),
		'closed' => getpercent($info['tickets']['closed'],$info['tickets']['total'])
	);
	($hook = FishHook::hook('roadmap_fetchmilestones')) ? eval($hook) : false;
	$milestones[] = $info;
}

addcrumb($uri->geturi(),l('roadmap'));

require(template('roadmap'));
?>