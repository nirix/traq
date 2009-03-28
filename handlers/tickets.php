<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

// Tickets Page
$breadcrumbs[$uri->anchor($project['slug'],'tickets')] = l('tickets');

FishHook::hook('projecthandler_tickets');

// Ticket Sorting
$sort = (isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'priority'); // Field to sort by
$order = (isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc'); // Direction to sort by

// Do filters
if(isset($_POST['add_filter']))
{
	if(!is_array($_POST['filters'])) $_POST['filters'] = array();
	foreach($_POST['filters'] as $type => $filter) {
		if(isset($_POST['rm_filter_'.$type]))
		{
			continue;
		}
		if(is_array($filter['values']))
		{
			$bits[] = $type.'='.$filter['mode'].implode(',',$filter['values']);
		}
		else
		{
			$bits[] = $type.'='.$filter['mode'].$filter['value'];
		}
	}
	if($_POST['add_filter'] != '' && !isset($_POST['filters'][$_POST['add_filter']]))
	{
		$bits[] = $_POST['add_filter'].'=';
	}
	$bits[] = 'sort='.$_POST['sort'].'&order='.$_POST['order'];
	$filters = implode('&',$bits);
	header("Location: ".$uri->geturi().'?'.$filters);
	exit;
}
$validfilters = array('component','description','milestone','owner','priority','reporter','severity','status','summary','type','version');
$filtermodes = array('!');
$filterbits = explode('&',$_SERVER['QUERY_STRING']);
foreach($filterbits as $filterbit)
{
	$bit = explode('=',$filterbit);
	if(in_array($bit[0],$validfilters))
	{
		if(in_array(substr($bit[1],0,1),$filtermodes))
		{
			$filter['mode'] = substr($bit[1],0,1);
			$bit[1] = substr($bit[1],1);
		}
		$filter['type'] = $bit[0];
		$filter['value'] = $bit[1];
		$filter['values'] = explode(',',$bit[1]);
		$filters[] = $filter;
		$filtersbits[] = $bit[0].'='.$bit[1];
		
		if($bit[1] != '')
			if($filter['type'] == 'milestone')
			{
				$milestone = $db->fetcharray($db->query("SELECT id,project,milestone FROM ".DBPREFIX."milestones WHERE project='".$db->escapestring($project['id'])."' AND milestone='".$db->escapestring($bit[1])."' LIMIT 1"));
				$query .= " AND milestoneid".$filter['mode']."='".$milestone['id']."'";
			}
			elseif($filter['type'] == 'status')
			{
				if($filter['value'] == 'open'
				or $filter['value'] == 'closed')
				{
					$query .= " AND status ".($filter['value'] == 'open' ? '>=1' : '<=0');
				}
				else
				{
					$query .= " AND (status=".implode(' OR status=',$filter['values']).")";
				}
			}
	}
}
$filterstring = implode('&',$filtersbits);

// Get Tickets
$tickets = array();
$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE projectid='".$project['id']."' $query ORDER BY $sort $order");
while($info = $db->fetcharray($fetchtickets))
{
	$info['summary'] = stripslashes($info['summary']); // Strip the slahes from the summary field
	$info['body'] = stripslashes($info['body']); // Strip the slahes from the body field
	$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
	$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
	$info['milestone'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$info['milestoneid']."' LIMIT 1")); // Get Milestone info
	FishHook::hook('projecthandler_tickets_all_fetchtickets');
	$tickets[] = $info;
}
unset($fetchtickets,$info);

FishHook::hook('projecthandler_tickets_all_pretemplate');

include(template('tickets'));

?>