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

require(template('newticket'));
?>