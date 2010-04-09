<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
 *
 * This file is part of Traq.
 *
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3
 * only, as published by the Free Software Foundation.
 *
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License version 3 for more details.
 *
 * You should have received a copy of the GNU General Public License
 * version 3 along with Traq. If not, see <http://www.gnu.org/licenses/>.
 *
 * $Id$
 */

// Check user permission
if(!$user->group['create_tickets'])
{
	$_SESSION['last_page'] = $uri->geturi();
	header("Location: ".$uri->anchor('user','login'));
}

// Include reCaptcha
require(TRAQPATH.'inc/recaptchalib.php');

addcrumb($uri->geturi(),l('new_ticket'));

// Do the New Ticket stuff...
include(TRAQPATH.'inc/ticket.class.php');
$ticket = new Ticket;

if(isset($_POST['summary']))
{
	// Check reCaptcha
	if(settings('recaptcha_enabled'))
	{
		$resp = recaptcha_check_answer(settings('recaptcha_privkey'),$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		
		if(!$resp->is_valid) {
			$recaptcha_error = $resp->error;
			$errors['recaptcha'] = true;
		}
	}
	
	// Set guest name cookie
	if(!$user->loggedin)
		setcookie('guestname',$_POST['name'],time()+50000,'/');
	
	// Ticket data array
	$data = array(
		'summary' => $_POST['summary'],
		'body' => $_POST['body'],
		'type' => $_POST['type'],
		'priority' => $_POST['priority'],
		'severity' => $_POST['severity'],
		'milestone_id' => $_POST['milestone'],
		'version_id' => $_POST['version'],
		'component_id' => (int)$_POST['component'],
		'assigned_to' => $_POST['assign_to'],
		'private' => $_POST['private'],
		'user_name' => $_POST['name']
	);
	
	// Check for errors
	if($ticket->check($data) && !count($errors))
	{
		$ticket->create($data);
		header("Location: ".$uri->anchor($project['slug'],'ticket-'.$ticket->ticket_id));
	}
	else
	{
		$errors = $ticket->errors;
		if(isset($recaptcha_error))
			$errors['recaptcha'] = l('error_recaptcha');
	}
}

($hook = FishHook::hook('handler_newticket')) ? eval($hook) : false;

require(template('new_ticket'));
?>