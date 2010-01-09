<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Check user permission
if(!$user->group['create_tickets'])
{
	$_SESSION['last_page'] = $uri->geturi();
	header("Location: ".$uri->anchor('user','login'));
}

// Fetch reCaptcha
require(TRAQPATH.'inc/recaptchalib.php');

addcrumb($uri->geturi(),l('new_ticket'));

($hook = FishHook::hook('newticket')) ? eval($hook) : false;

// Do the New Ticket stuff...
include(TRAQPATH.'inc/ticket.class.php');
$ticket = new Ticket;

$resp = recaptcha_check_answer(settings('recaptcha_privkey'),$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);

if(!$resp->is_valid) {
	$recaptcha_error = $resp->error;
}

if(isset($_POST['summary']))
{
	$data = array(
		'summary' => $_POST['summary'],
		'body' => $_POST['body'],
		'type' => $_POST['type'],
		'priority' => $_POST['priority'],
		'severity' => $_POST['severity'],
		'milestone_id' => $_POST['milestone'],
		'version_id' => $_POST['version'],
		'component_id' => $_POST['component'],
		'assigned_to' => $_POST['assign_to'],
		'private' => $_POST['private']
	);
	if($ticket->create($data) && !count($errors))
	{
		header("Location: ".$uri->anchor(PROJECT_SLUG,'ticket-'.$ticket->ticket_id));
	}
	else
	{
		$errors = $ticket->errors;
		if(isset($recaptcha_error))
			$errors['recaptcha'] = l('error_recaptcha');
	}
}

require(template('newticket'));
?>