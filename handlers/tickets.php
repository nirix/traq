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
	if(!is_array($_POST['filters'])) $_POST['filters'] = array();
	$url = array();
	
	// Check if we're adding a filter
	$newfilters = array();
	if(in_array($_POST['add_filter'],ticket_filters()) AND !isset($_POST['filters'][$_POST['add_filter']]))
	{
		$newfilters[] = $_POST['add_filter'].'=';
	}
	
	// Filters
	foreach($_POST['filters'] as $filter => $values)
	{		
		$val = -1;
		// Milestone, Version, Type, Component
		if(in_array($filter,array('milestone','version','type','component')))
		{
			// Loop through values
			$bits = array();
			foreach($values as $value)
			{
				$val++;
				// Check if its not empty and set to be removed.
				if(!empty($value['value']) && !isset($_POST['rmfilter'][$filter][$val]))
					$bits[] = $value['value'];
			}
			
			// Check if we're adding a filter.
			if($_POST['add_filter'] == $filter)
				$bits[] = '';
			
			// If theres values, add it to the URL.
			if(count($bits))
				$url[] = $filter.'='.$_POST['modes'][$filter].implode(',',$bits);
		}
		// Status
		elseif($filter == 'status' && !isset($_POST['rmfilter']['status']))
		{
			// Add it to the URL.
			$url[] = 'status='.implode(',',$values);
		}
	}
	
	// Columns
	$url[] = 'columns='.implode(',',$_POST['columns']);
	
	// Build the URL and redirect.
	header("Location: ".$uri->geturi().'?'.implode('&',array_merge($url,$newfilters)));
}

// Ticket Sorting
$sort = (isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'priority'); // Field to sort by
$order = (isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc'); // Direction to sort by

// Filters
$filters = array();
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
			$filter['values'] = explode(',',substr($bit[1],1));
		}
		
		// Add filter to the filters array.
		$filters[] = $filter;
		
		// Create an empty array for the values
		// used to make the query.
		$values = array();

		// Check if the filter value is not blank
		if(empty($filter['value'])) { continue; }
		
		// Milestone filter
		if($filter['type'] == 'milestone')
		{
			// Loop through the values
			foreach($filter['values'] as $value)
			{
				// Make sure the value is not empty.
				if(empty($value))
					continue;
				
				// Fetch the milestone info and get the ID for the query.
				$milestone = $db->fetcharray($db->query("SELECT id,project_id,milestone FROM ".DBPF."milestones WHERE project_id='".$db->res($project['id'])."' AND slug='".$db->res(urldecode($value))."' LIMIT 1"));
				$values[] = $milestone['id'];
			}
			if(count($values))
				$query .= " AND (milestone_id".$filter['mode']."=".implode(' '.($filter['mode'] == '!' ? 'AND' : 'OR').' milestone_id'.$filter['mode'].'=',$values).")";
		}
		// Version filter
		elseif($filter['type'] == 'version')
		{
			// Loop through the values
			foreach($filter['values'] as $value)
			{
				// Make sure the value is not empty.
				if(empty($value))
					continue;
				
				$values[] = $value;
			}
			if(count($values))
				$query .= " AND (version_id".$filter['mode']."=".implode(' '.($filter['mode'] == '!' ? 'AND' : 'OR').' version_id'.$filter['mode'].'=',$values).")";
		}
		// Type filter
		elseif($filter['type'] == 'type')
		{
			// Loop through the values
			foreach($filter['values'] as $value)
			{
				// Make sure the value is not empty.
				if(empty($value))
					continue;
				
				$values[] = $value;
			}
			
			if(count($values))
				$query .= " AND (type".$filter['mode']."=".implode(' '.($filter['mode'] == '!' ? 'AND' : 'OR').' type'.$filter['mode'].'=',$values).")";
		}
		// Component filter
		elseif($filter['type'] == 'component')
		{
			// Loop through the values
			foreach($filter['values'] as $value)
			{
				// Make sure the value is not empty.
				if(empty($value))
					continue;
				
				$values[] = $value;
			}
			if(count($values))
				$query .= " AND (component_id".$filter['mode']."=".implode(' '.($filter['mode'] == '!' ? 'AND' : 'OR').' component_id'.$filter['mode'].'=',$values).")";
				//die($query);
		}
		// Status filter
		elseif($filter['type'] == 'status')
		{
			// If the value is 'open' or 'closed' get the ID's from the DB.
			if($filter['value'] == 'open'
			or $filter['value'] == 'closed')
			{
				// Build the $status array for the query.
				$status = array('open'=>array(),'closed'=>array());
				foreach(ticket_status_list() as $row)
					$status['open'][] = $row['id'];
				foreach(ticket_status_list(0) as $row)
					$status['closed'][] = $row['id'];
				
				$filter['values'] = ($filter['value'] == 'open' ? $status['open'] : $status['closed']);
				
				$query .= " AND (status=".implode(' OR status=',$filter['values']).")";
			}
			// ID's are in the URL.
			else
			{
				$query .= " AND (status=".implode(' OR status=',$filter['values']).")";
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
	$info['version'] = $db->fetcharray($db->query("SELECT * FROM ".DBPF."versions WHERE id='".$info['version_id']."' LIMIT 1")); // Get Version info
	$info['assignee'] = $db->fetcharray($db->query("SELECT id, username FROM ".DBPF."users WHERE id='".$info['assigned_to']."' LIMIT 1")); // Get assignee info
	($hook = FishHook::hook('tickets_fetchtickets')) ? eval($hook) : false;
	$tickets[] = $info;
}

($hook = FishHook::hook('handler_tickets')) ? eval($hook) : false;

require(template('tickets'));
?>