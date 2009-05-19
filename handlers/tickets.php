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

($hook = FishHook::hook('tickets_start')) ? eval($hook) : false;

// Ticket Sorting
$sort = (isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'priority'); // Field to sort by
$order = (isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc'); // Direction to sort by

// Add a filter
if(isset($_POST['update']))
{
	// Check if there are any filters already and
	// include them in the query string
	if(!is_array($_POST['filters'])) $_POST['filters'] = array();
	foreach($_POST['filters'] as $type => $filter) {
		if(isset($_POST['rm_filter_'.$type]))
		{
			// If this filter is set to be removed
			continue;
		}
		
		// Check if the filter has multiple values
		// if so then implode then with a comma.
		if(is_array($filter['values']))
		{
			$bits[] = $type.'='.$filter['mode'].implode(',',$filter['values']);
		}
		else
		{
			$bits[] = $type.'='.$filter['mode'].$filter['value'];
		}
	}
	
	// Make sure this filter doesn't already exist
	// any filters that can have multiple values
	// seperate them with a comma and not multiple
	// query string variables.
	if($_POST['add_filter'] != '' && !isset($_POST['filters'][$_POST['add_filter']]))
	{
		$bits[] = $_POST['add_filter'].'=';
	}
	
	// Add the sort and order vars
	$bits[] = 'sort='.$_POST['sort'].'&order='.$_POST['order'];
	
	// Build the filters query string
	$filters = implode('&',$bits);
	
	// Do the columns
	if(!is_array($_POST['column'])) $_POST['column'] = $columns = array();
	
	foreach($_POST['column'] as $col => $val)
	{
		$columns[] = $col;	
	}
	$cols = implode(',',$columns);
	$cols = '&columns='.$cols;
	
	header("Location: ".$uri->geturi().'?'.$filters.$cols);
	exit;
}

// Create arrays now so if they are blank
// we dont get any errors later.
$filters = array();
$filtersbits = array();

// Valid filters and filter modes
$validfilters = array('component','description','milestone','owner',
					  'priority','reporter','severity','status',
					  'summary','type','version'
					  );
$filtermodes = array('!');

// Decode the filters from the query string.
$query = '';
$filterbits = explode('&',$_SERVER['QUERY_STRING']);
foreach($filterbits as $filterbit)
{
	$bit = explode('=',$filterbit);
	
	// Make sure its a valid filter and not
	// some other query string var.
	if(in_array($bit[0],$validfilters))
	{
		// Check if the filter has a mode.
		if(in_array(substr($bit[1],0,1),$filtermodes))
		{
			$filter['mode'] = substr($bit[1],0,1);
			$bit[1] = substr($bit[1],1);
		}
		
		// Make the filter array.
		$filter['type'] = $bit[0];
		$filter['value'] = $bit[1];
		$filter['values'] = explode(',',$bit[1]);
		$filters[] = $filter;
		$filtersbits[] = $bit[0].'='.$bit[1];
		
		// Check if the filter value is not blank
		if($bit[1] != '')
			// Do the milestone filter
			if($filter['type'] == 'milestone')
			{
				$milestone = $db->fetcharray($db->query("SELECT id,project,milestone FROM ".DBPREFIX."milestones WHERE project='".$db->escapestring($project['id'])."' AND milestone='".$db->escapestring($bit[1])."' LIMIT 1"));
				$query .= " AND milestoneid".$filter['mode']."='".$milestone['id']."'";
			}
			// Do the status filter
			elseif($filter['type'] == 'status')
			{
				// All open or closed
				if($filter['value'] == 'open'
				or $filter['value'] == 'closed')
				{
					$query .= " AND status ".($filter['value'] == 'open' ? '>=1' : '<=0');
				}
				// Different status values
				else
				{
					$query .= " AND (status=".implode(' OR status=',$filter['values']).")";
				}
			}
			elseif($filter['type'] == 'priority')
			{
				$query .= " AND priority='".$db->escapestring($filter['value'])."'";	
			}
	}
}
$filterstring = implode('&',$filtersbits);

// Do columns stuff...
if(!isset($_REQUEST['columns']))
{
	$_REQUEST['columns'] = 'ticket,summary,status,owner,type,priority,component,milestone';
}

$columns = explode(',',$_REQUEST['columns']);

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
	$info['assignee'] = $db->fetcharray($db->query("SELECT id, username FROM ".DBPREFIX."users WHERE id='".$info['assigneeid']."' LIMIT 1")); // Get assignee info
	($hook = FishHook::hook('tickets_fetchtickets')) ? eval($hook) : false;
	$tickets[] = $info;
}
unset($fetchtickets,$info);

($hook = FishHook::hook('tickets_end')) ? eval($hook) : false;

include(template('tickets'));
?>