<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

class Ticket
{
	public $info = NULL; // Used to store the ticket info.
	
	/**
	 * Create ticket
	 * Used to easily create new tickets.
	 */
	public function create($data)
	{
		global $db,$project,$user;
		
		// Check fields for errors.
		if(empty($data['summary']))
			$errors['summary'] = l('error_summary_empty');
		
		if(empty($data['body']))
			$errors['body'] = l('error_body_empty');
			
		if(empty($data['name']) && !$user->loggedin)
			$errors['name'] = l('error_name_empty');
		
		if(count($errors))
		{
			$this->errors = $errors;
			return false;
		}
		
		// Set some required fields.
		if(!isset($data['private']))
			$data['private'] = 0;
			
		if(!$user->loggedin)
			$name = $data['name'];
		else
			$name = $user->info['login'];
		
		// Insert the ticket into the database.
		$db->query("INSERT INTO ".DBPF."tickets VALUES(
			0,
			'".$db->res($project['next_tid'])."',
			'".$db->res($data['summary'])."',
			'".$db->res($data['body'])."',
			'".$db->res($user->info['id'])."',
			'".$db->res($name)."',
			'".$db->res($project['id'])."',
			'".$db->res($data['milestone'])."',
			'".$db->res($data['version'])."',
			'".$db->res($data['component'])."',
			'".$db->res($data['type'])."',
			'1',
			'".$db->res($data['priority'])."',
			'".$db->res($data['severity'])."',
			'".$db->res($data['assign_to'])."',
			'0',
			'".time()."',
			'0',
			'".$db->res($data['private'])."'
			)
		");
		
		// Set the ticket ID.
		$this->ticket_id = $project['next_tid'];
		
		// Update Project's 'next_tid' field.
		$db->query("UPDATE ".DBPF."projects SET next_tid=next_tid+1 WHERE id='".$db->res($project['id'])."' LIMIT 1");
		
		return true;
	}
	
	/**
	 * Get Ticket
	 * Used to easily fetch a tickets info.
	 */
	public function get($args)
	{
		global $db;
		
		if(!array($args))
		{
			$args = func_get_args();
		}
		
		// Check which arguments are set and compile the query.
		$query = array();
		
		if(isset($args['id']))
			$query[] = "id='".$args['id']."'";
			
		if(isset($args['ticket_id']))
			$query[] = "ticket_id='".$args['ticket_id']."'";
			
		if(isset($args['project_id']))
			$query[] = "project_id='".$args['project_id']."'";
			
		$query = implode(' AND ',$query);
		
		$ticket = $db->queryfirst("SELECT * FROM ".DBPF."tickets WHERE $query LIMIT 1"); // Fetch the ticket info
		$ticket['milestone'] = $db->queryfirst("SELECT * FROM ".DBPF."milestones WHERE id='".$db->res($ticket['milestone_id'])."' LIMIT 1"); // Fetch the milestone info
		$ticket['version'] = $db->queryfirst("SELECT * FROM ".DBPF."versions WHERE id='".$db->res($ticket['version_id'])."' LIMIT 1"); // Fetch the version info
		$ticket['component'] = $db->queryfirst("SELECT * FROM ".DBPF."components WHERE id='".$db->res($ticket['component_id'])."' LIMIT 1"); // Fetch the component info
		
		// For now, just make the attachments an empty array,
		// this hides the errors.
		$ticket['attachments'] = array();
		
		// Store the ticket info and clear the $ticket array.
		$this->info = $ticket;
		unset($ticket);
		
		return $this->info;
	}
	
	/**
	 * Delete ticket
	 * Used to delete a ticket.
	 */
	public function delete($args)
	{
		global $db;
		
		if(!array($args))
		{
			$args = func_get_args();
		}
		
		
		// Check which arguments are set and compile the query.
		$query = array();
		
		if(isset($args['id']))
			$query[] = "id='".$args['id']."'";
			
		if(isset($args['ticket_id']))
			$query[] = "ticket_id='".$args['ticket_id']."'";
			
		if(isset($args['project_id']))
			$query[] = "project_id='".$args['project_id']."'";
			
		$query = implode(' AND ',$query);
		
		$db->query("DELETE * FROM ".DBPF."tickets WHERE $query LIMIT 1");
	}
}
?>