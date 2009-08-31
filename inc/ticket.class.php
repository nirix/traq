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
		
		// Insert into the DB.
		if(!isset($data['private']))
			$data['private'] = 0;
			
		if(!$user->loggedin)
			$name = $data['name'];
		else
			$name = $user->info['login'];
		
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
			'".$db->res($data['status'])."',
			'".$db->res($data['priority'])."',
			'".$db->res($data['severity'])."',
			'".$db->res($data['assign_to'])."',
			'0',
			'".time()."',
			'0',
			'".$db->res($data['private'])."'
			)
		");
		
		$this->ticket_id = $project['next_tid'];
		
		// Update Project's 'next_tid' field.
		$db->query("UPDATE ".DBPF."projects SET next_tid=next_tid+1 WHERE id='".$db->res($project['id'])."' LIMIT 1");
		
		return true;
	}
}
?>