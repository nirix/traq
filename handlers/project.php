<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

$project = $db->queryfirst("SELECT * FROM ".DBPF."projects WHERE slug='".$db->es($uri->seg[0])."' LIMIT 1");
$project['managers'] = explode(',',$project['managers']);

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
	require(TRAQPATH.'handlers/milestone.php');
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