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
	public $errors = array();
	
	/**
	 * Create ticket
	 * Used to easily create new tickets.
	 *
	 * @param array $data The ticket data array.
	 * @return bool
	 */
	public function create($data)
	{
		global $db,$project,$user;
		
		// Check fields for errors.
		if(!$this->check($data))
			return false;
		
		// Set some required fields.
		$data['ticket_id'] = $project['next_tid'];
		$data['project_id'] = $project['id'];
		if(!isset($data['private']))
			$data['private'] = 0;
		if(!isset($data['created']))
			$data['created'] = time();
		
		// Check if the user is logged in, if so use their info
		// if not use info from data array.
		if(!$user->loggedin)
		{
			$data['user_id'] = 0;
		}
		else
		{
			$data['user_name'] = $user->info['username'];
			$data['user_id'] = $user->info['id'];
		}
		
		($hook = FishHook::hook('ticket_create')) ? eval($hook) : false;
		
		// Sort out the data fields and values for the query.
		$fields = array();
		$values = array();
		foreach($data as $field => $value)
		{
			$fields[] = $db->res($field);
			$values[] = "'".$db->res($value)."'";
		}
		$fields = implode(',',$fields);
		$values = implode(',',$values);
		
		// Insert the ticket into the database.
		$db->query("INSERT INTO ".DBPF."tickets
			(".$fields.")
			VALUES(".$values.")
		");
		$ticketid = $db->insertid();
		
		// Insert the ticket history row
		$db->query("INSERT INTO ".DBPF."ticket_history (user_id,user_name,timestamp,ticket_id,project_id,changes,comment) VALUES(
			'".$user->info['id']."',
			'".$user->info['username']."',
			'".time()."',
			'".$ticketid."',
			'".$project['id']."',
			'".json_encode(array(array('property'=>'status','from'=>'','to'=>'1','action'=>'open')))."',
			''
		)");
		
		// Insert the timeline row
		$db->query("INSERT INTO ".DBPF."timeline VALUES(
			0,
			'".$db->res($project['id'])."',
			'".$ticketid."',
			'open_ticket',
			'".$project['next_tid']."',
			'".$user->info['id']."',
			'".$db->res($data['user_name'])."',
			'".time()."',
			NOW()
		)");
		
		// Set the ticket ID.
		$this->ticket_id = $project['next_tid'];
		
		// Update Project's 'next_tid' field.
		$db->query("UPDATE ".DBPF."projects SET next_tid=next_tid+1 WHERE id='".$db->res($project['id'])."' LIMIT 1");
		
		return true;
	}
	
	/**
	 * Check Data
	 * Checks the ticket data for errors.
	 *
	 * @param array $data Ticket data array.
	 */
	public function check($data)
	{
		// Check summary
		if(empty($data['summary']))
			$errors['summary'] = l('error_summary_empty');
		
		// Check body
		if(empty($data['body']))
			$errors['body'] = l('error_body_empty');
		
		// Check user name
		if(empty($data['user_name']) && !$user->loggedin)
			$errors['name'] = l('error_name_empty');
		
		// If theres errors, push them to the error
		// array and return false.
		if(count($errors))
		{
			$this->errors = $errors;
			return false;
		}
		
		// No errors, return true.
		return true;
	}
	
	/**
	 * Get Ticket
	 * Used to easily fetch a tickets info.
	 *
	 * @param array $args Arguments for the fetch ticket query.
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
		
		// Build the arguments query block.
		$query = implode(' AND ',$query);
		
		// Fetch the ticket, milestone, version, component and assignee info.
		$ticket = $db->queryfirst("SELECT * FROM ".DBPF."tickets WHERE $query LIMIT 1"); // Fetch the ticket info
		$ticket['milestone'] = $db->queryfirst("SELECT * FROM ".DBPF."milestones WHERE id='".$db->res($ticket['milestone_id'])."' LIMIT 1"); // Fetch the milestone info
		$ticket['version'] = $db->queryfirst("SELECT * FROM ".DBPF."versions WHERE id='".$db->res($ticket['version_id'])."' LIMIT 1"); // Fetch the version info
		$ticket['component'] = $db->queryfirst("SELECT * FROM ".DBPF."components WHERE id='".$db->res($ticket['component_id'])."' LIMIT 1"); // Fetch the component info
		$ticket['assignee'] = $db->queryfirst("SELECT * FROM ".DBPF."users WHERE id='".$db->res($ticket['assigned_to'])."' LIMIT 1"); // Fetch the assignee info
		
		// For now, just make the attachments an empty array,
		// this hides the errors.
		$ticket['attachments'] = array();
		
		// Store the ticket info and clear the $ticket array.
		$this->info = $ticket;
		unset($ticket);
		
		($hook = FishHook::hook('ticket_get')) ? eval($hook) : false;
		
		return $this->info;
	}
	
	/**
	 * Delete ticket
	 * Used to delete a ticket.
	 *
	 * @param int $id The ID of the ticket (row ID, not ticket_id).
	 */
	public function delete($id)
	{
		global $db;
		
		// Delete ticket
		$db->query("DELETE FROM ".DBPF."tickets WHERE id='".$id."' LIMIT 1");
		
		// Delete ticket history
		$db->query("DELETE FROM ".DBPF."ticket_history WHERE ticket_id='".$id."'");
		
		// Delete timeline references
		$db->query("DELETE FROM ".DBPF."timeline WHERE owner_id='".$id."' AND (action='open_ticket' OR action='reopen_ticket' OR action='close_ticket')");
	
		($hook = FishHook::hook('ticket_delete')) ? eval($hook) : false;
	}
}
?>