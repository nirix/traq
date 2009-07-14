<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

$project = $db->queryfirst("SELECT * FROM ".DBPF."projects WHERE slug='".$db->es($uri->seg[0])."' LIMIT 1");

($hook = FishHook::hook('project_handler')) ? eval($hook) : false;

if($uri->seg[1] == '')
{	
	require(template('project_info'));
}
elseif(preg_match('/ticket-(?<id>\d+)/',$uri->seg[1],$matches))
{
	require(TRAQPATH.'handlers/ticket.php');
}
elseif(preg_match('/milestone-(?<slug>.*)/',$uri->seg[1],$matches))
{
	$milestone = $db->queryfirst("SELECT * FROM ".DBPF."milestones WHERE slug='".$db->es($matches['slug'])."' AND project_id='".$project['id']."' LIMIT 1");
	$milestone['tickets'] = array();
	$milestone['tickets']['open'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$milestone['id']."' AND project_id='".$project['id']."' AND closed='0' LIMIT 1"));
	$milestone['tickets']['closed'] = $db->numrows($db->query("SELECT * FROM ".DBPF."tickets WHERE milestone_id='".$milestone['id']."' AND project_id='".$project['id']."' AND closed='1' LIMIT 1"));
	$milestone['tickets']['total'] = ($info['tickets']['open']+$info['tickets']['closed']);
	$milestone['tickets']['percent'] = array(
		'open' => ($milestone['tickets']['open'] ? getpercent($milestone['tickets']['open'],$milestone['tickets']['total']) : 0),
		'closed' => getpercent($milestone['tickets']['closed'],$milestone['tickets']['total'])
	);
	
	addcrumb($uri->anchor($project['slug'],'roadmap'),l('roadmap'));
	addcrumb($uri->geturi(),l('milestone').': '.$milestone['milestone']);
	
	require(template('milestone'));
}
elseif($uri->seg[1] == 'roadmap')
{
	require(TRAQPATH.'handlers/roadmap.php');
}
elseif($uri->seg[1] == 'tickets')
{
	require(TRAQPATH.'handlers/tickets.php');
}
elseif($uri->seg[1] == 'newticket')
{
	require(TRAQPATH.'handlers/newticket.php');
}
?>