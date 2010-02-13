<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

($hook = FishHook::hook('tickets_handler')) ? eval($hook) : false;

addcrumb($uri->geturi(),l('tickets'));

// Update Filters and Columns
if(isset($_POST['columns']) or isset($_POST['filter']))
{
	$url = '';
	
	// Filters
	
	// Columns
	$url .= 'columns='.implode(',',$_POST['columns']);
	
	header("Location: ".$uri->geturi().'?'.$url);
}

// Ticket Sorting
$sort = (isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'priority'); // Field to sort by
$order = (isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc'); // Direction to sort by

// Filters
$query = '';
foreach(explode('&',$_SERVER['QUERY_STRING']) as $filter)
{
	$bit = explode('=',$filter);

	// Make sure its a valid filter and not
	// some other query string var.
	if(in_array($bit[0],ticket_filters()))
	{
		// Make the filter array.
		$filter = array(
			'type' => $bit[0],
			'value' => $bit[1],
			'values' => explode(',',$bit[1]),
		);
		
		// Check if the filter has a mode.
		if(substr($bit[1],0,1) == '!')
		{
			$filter['mode'] = substr($bit[1],0,1);
			$filter['value'] = substr($bit[1],1);
		}

		// Check if the filter value is not blank
		if(empty($filter['value'])) { continue; }
		
		// Milestone filter
		if($filter['type'] == 'milestone')
		{
			$milestone = $db->fetcharray($db->query("SELECT id,project_id,milestone FROM ".DBPF."milestones WHERE project_id='".$db->res($project['id'])."' AND milestone='".$db->res(urldecode($filter['value']))."' LIMIT 1"));
			$query .= " AND milestone_id".$filter['mode']."='".$milestone['id']."'";
		}
		// Version filter
		elseif($filter['type'] == 'version')
		{
			$query .= " AND version_id".$filter['mode']."='".$db->res($filter['value'])."'";	
		}
		// Status filter
		elseif($filter['type'] == 'status')
		{
			if($filter['value'] == 'open'
			or $filter['value'] == 'closed')
			{
				$status = array('open'=>array(),'closd'=>array());
				foreach(ticket_status_list() as $row)
					$status['open'][] = $row['id'];
				foreach(ticket_status_list(0) as $row)
					$status['closed'][] = $row['id'];
				
				$query .= " AND (status=".implode(' OR status=',($filter['value']=='open' ? $status['open'] : $status['closed'])).")";
			}
		}
	}
}

// Columns
if(!isset($_REQUEST['columns']))
{
	$_REQUEST['columns'] = 'ticket,summary,status,owner,type,priority,component,milestone';
}
$columns = explode(',',$_REQUEST['columns']);
($hook = FishHook::hook('tickets_columns')) ? eval($hook) : false;

// Get Tickets
$tickets = array();
$fetchtickets = $db->query("SELECT * FROM ".DBPF."tickets WHERE project_id='".$project['id']."' $query ORDER BY $sort $order");
while($info = $db->fetcharray($fetchtickets))
{
	$info['summary'] = stripslashes($info['summary']); // Strip the slahes from the summary field
	$info['body'] = stripslashes($info['body']); // Strip the slahes from the body field
	$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPF."components WHERE id='".$info['component_id']."' LIMIT 1")); // Get Component info
	$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
	$info['milestone'] = $db->fetcharray($db->query("SELECT * FROM ".DBPF."milestones WHERE id='".$info['milestone_id']."' LIMIT 1")); // Get Milestone info
	$info['version'] = $db->fetcharray($db->query("SELECT * FROM ".DBPF."versions WHERE id='".$info['versionid']."' LIMIT 1")); // Get Version info
	$info['assignee'] = $db->fetcharray($db->query("SELECT id, username FROM ".DBPF."users WHERE id='".$info['assigned_to']."' LIMIT 1")); // Get assignee info
	($hook = FishHook::hook('tickets_fetchtickets')) ? eval($hook) : false;
	$tickets[] = $info;
}

require(template('tickets'));
?>