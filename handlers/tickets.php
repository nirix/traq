<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
 *
 * This file is part of Traq.
 * 
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
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

($hook = FishHook::hook('handler_tickets')) ? eval($hook) : false;

addcrumb($uri->geturi(),l('tickets'));

// Update Filters and Columns
if(isset($_POST['columns']) or isset($_POST['filter']))
{
	if(!is_array($_POST['filters'])) $_POST['filters'] = array();
	$url = array();
	
	// Check if we're adding a filter
	$newfilters = array();
	if(in_array($_POST['add_filter'],ticket_filters()) and !isset($_POST['filters'][$_POST['add_filter']]))
		$newfilters[] = $_POST['add_filter'].'=';
	
	// Filters
	foreach($_POST['filters'] as $filter => $values)
	{		
		$val = -1;
		// Milestone, Version, Type, Component, Severity, Priority, Owner
		if(in_array($filter,array('milestone','version','type','component','severity','priority','owner')))
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
$order = (isset($_REQUEST['order']) ? $_REQUEST['order'] : 'DESC'); // Direction to sort by

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
				$query .= "AND milestone_id ".iif(isset($filter['mode']) && $filter['mode'] == '!','not ')."in (".implode(',',$values).") ";
		}
		// Version, Tpye and Component filter
		elseif(in_array($filter['type'],array('version','type','component','severity','priority')))
		{
			// Loop through the values
			foreach($filter['values'] as $value)
			{
				// Make sure the value is not empty.
				if(empty($value))
					continue;
				
				$values[] = $value;
			}
			
			switch($filter['type'])
			{
				case "version":
				case "component":
					$type = $filter['type'].'_id';
				break;
				
				default:
					$type = $filter['type'];
			}
			
			if(count($values))
				$query .= "AND ".$type." ".iif(isset($filter['mode']) && $filter['mode'] == '!','not ')."in (".implode(',',$values).") ";
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
			}
			$query .= "AND status ".iif(isset($filter['mode']) && $filter['mode'] == '!','not ')."in (".implode(',',$filter['values']).") ";
		}
		// Owner filter
		elseif($filter['type'] == 'owner')
		{
			// Loop through the values
			foreach($filter['values'] as $value)
			{
				// Make sure the value is not empty.
				if(empty($value))
					continue;
				
				$values[] = "'".$value."'";
			}
			
			if(count($values))
				$query .= "AND user_name ".iif(isset($filter['mode']) && $filter['mode'] == '!','not ')."in (".implode(',',$values).") ";
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
$fetchtickets = $db->query("SELECT * FROM ".DBPF."tickets WHERE project_id='".$project['id']."' $query ORDER BY ".$db->res(($sort == 'updated' ? 'IF(updated < 1, created, updated)' : $sort))." ".$db->res($order));
while($info = $db->fetcharray($fetchtickets))
{
	$info['summary'] = stripslashes($info['summary']); // Strip the slahes from the summary field
	$info['body'] = stripslashes($info['body']); // Strip the slahes from the body field
	$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPF."components WHERE id='".$info['component_id']."' LIMIT 1")); // Get Component info
	$info['owner'] = $user->getinfo($info['user_id']); // Get owner info
	$info['milestone'] = $db->fetcharray($db->query("SELECT * FROM ".DBPF."milestones WHERE id='".$info['milestone_id']."' LIMIT 1")); // Get Milestone info
	$info['version'] = $db->fetcharray($db->query("SELECT * FROM ".DBPF."versions WHERE id='".$info['version_id']."' LIMIT 1")); // Get Version info
	$info['assignee'] = $db->fetcharray($db->query("SELECT id, username FROM ".DBPF."users WHERE id='".$info['assigned_to']."' LIMIT 1")); // Get assignee info
	($hook = FishHook::hook('tickets_fetchtickets')) ? eval($hook) : false;
	$tickets[] = $info;
}

($hook = FishHook::hook('handler_tickets')) ? eval($hook) : false;

require(template('tickets'));
?>