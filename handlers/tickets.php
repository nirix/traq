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
	
	// Check if we're removing a filter...
	/*if(isset($_POST['rmfilter'])) {
		foreach($_POST['rmfilter'] as $rm => $val)
			//die($rm);
			$rm = explode('_',$rm);
			//print_r($_POST['filters'][$rm[0]][$rm[1]]);
			unset($_POST['filters'][$rm[0]][$rm[1]]);
	}*/
	
	//print_r($_POST['rmfilter']);
	
	// Filters
	foreach($_POST['filters'] as $filter => $values)
	{		
		$val = -1;
		// Milestone
		if($filter == 'milestone')
		{
			$milestones = array();
			foreach($values as $value)
			{
				$val++;
				if(!empty($value['value']) && !isset($_POST['rmfilter'][$filter][$val]))
					$milestones[] = $value['value'];
			}
			
			if($_POST['add_filter'] == 'milestone')
				$milestones[] = '';
			
			if(count($milestones))
				$url[] = 'milestone='.$_POST['modes']['milestone'].implode(',',$milestones);
		}
		// Version
		if($filter == 'version')
		{
			$versions = array();
			foreach($values as $value)
			{
				$val++;
				if(!empty($value['value']) && !isset($_POST['rmfilter'][$filter][$val]))
					$versions[] = $value['value'];
			}
			
			if($_POST['add_filter'] == 'version')
				$versions[] = '';
			
			if(count($versions))
			$url[] = 'version='.$_POST['modes']['version'].implode(',',$versions);
		}
		// Type
		if($filter == 'type')
		{
			$types = array();
			foreach($values as $value)
			{
				$val++;
				if(!empty($value['value']) && !isset($_POST['rmfilter'][$filter][$val]))
					$types[] = $value['value'];
			}
			
			if($_POST['add_filter'] == 'type')
				$types[] = '';
			
			if(count($types))
			$url[] = 'type='.$_POST['modes']['type'].implode(',',$types);
		}
		// Status
		elseif($filter == 'status' && !isset($_POST['rmfilter']['status']))
		{
			$url[] = 'status='.implode(',',$values);
		}
	}
	
	// Columns
	$url[] = 'columns='.implode(',',$_POST['columns']);
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

		// Check if the filter value is not blank
		if(empty($filter['value'])) { continue; }
		
		// Milestone filter
		if($filter['type'] == 'milestone')
		{
			// Loop through the values
			$empty = 0;
			foreach($filter['values'] as $value)
			{	
				if(empty($value))
				{
					$empty++;
					continue;
				}
				
				// Fetch the milestone info and get the ID for the query.
				$milestone = $db->fetcharray($db->query("SELECT id,project_id,milestone FROM ".DBPF."milestones WHERE project_id='".$db->res($project['id'])."' AND slug='".$db->res(urldecode($value))."' LIMIT 1"));
				$values[] = $milestone['id'];
			}
			if(count($values) != $empty)
				$query .= " AND (milestone_id".$filter['mode']."=".implode(' '.($filter['mode'] == '!' ? 'AND' : 'OR').' milestone_id'.$filter['mode'].'=',$values).")";
		}
		// Version filter
		if($filter['type'] == 'version')
		{
			// Loop through the values
			$empty = 0;
			foreach($filter['values'] as $value)
			{	
				if(empty($value))
				{
					$empty++;
					continue;
				}
				
				$values[] = $value;
			}
			if(count($values) != $empty)
				$query .= " AND (version_id".$filter['mode']."=".implode(' '.($filter['mode'] == '!' ? 'AND' : 'OR').' version_id'.$filter['mode'].'=',$values).")";
		}
		// Type filter
		if($filter['type'] == 'type')
		{
			// Loop through the values
			$empty = 0;
			foreach($filter['values'] as $value)
			{	
				if(empty($value))
				{
					$empty++;
					continue;
				}
				
				$values[] = $value;
			}
			if(count($values) != $empty)
				$query .= " AND (type".$filter['mode']."=".implode(' '.($filter['mode'] == '!' ? 'AND' : 'OR').' type'.$filter['mode'].'=',$values).")";
		}
		// Status filter
		elseif($filter['type'] == 'status')
		{
			if($filter['value'] == 'open'
			or $filter['value'] == 'closed')
			{
				$status = array('open'=>array(),'closed'=>array());
				foreach(ticket_status_list() as $row)
					$status['open'][] = $row['id'];
				foreach(ticket_status_list(0) as $row)
					$status['closed'][] = $row['id'];
				
				$filter['values'] = ($filter['value'] == 'open' ? $status['open'] : $status['closed']);
				
				$query .= " AND (status=".implode(' OR status=',($filter['value']=='open' ? $status['open'] : $status['closed'])).")";
			}
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

require(template('tickets'));
?>