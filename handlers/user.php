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

// Login
if($uri->seg[1] == "login")
{
	// Check if the form has been submitted.
	if($_POST['action'] == 'login')
	{
		// Check if there were errors, if not go back to where we came from.
		if($user->login($_POST['username'],$_POST['password'],$_POST['remember']))
			header("Location: ".($_POST['goto'] != '' ? $_POST['goto'] : $uri->anchor()));
	}
	include(template('user/login'));
}
// Register
elseif($uri->seg[1] == "register" && settings('allow_registration'))
{
	// Include reCaptcha
	require(TRAQPATH.'inc/recaptchalib.php');
	
	// Check if the form has been submitted.
	if($_POST['action'] == 'register')
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
		
		// User data array.
		$data = array(
			'username' => $_POST['username'],
			'password' => $_POST['password'],
			'password2' => $_POST['password2'],
			'email' => $_POST['email'],
			'name' => $_POST['name']
		);
		
		// Check if there were errors, if not go to the UserCP.
		if(!count($errors))
		{
			if($user->register($data))
				header("Location: ".$uri->anchor('user','login'));
		}
		else
		{
			$user->errors['recaptcha'] = l('error_recaptcha');
		}
	}
	include(template('user/register'));
}
// Logout
elseif($uri->seg[1] == "logout")
{
	$user->logout();
	header("Location: ".$uri->anchor());
}
elseif($uri->seg[1] == "usercp")
{
	// Update user info
	if($_POST['action'] == 'save')
	{
		// Check for errors
		$errors = array();
		// Check email
		if(empty($_POST['email']))
			$errors['email'] = l('error_email_empty');
		// Check password
		if(sha1($_POST['password']) != $user->info['password'])
			$errors['password'] = l('error_enter_password');
		// Check if new password is not blank
		if(empty($_POST['new_password']))
			//$errors['new_password'] = l('error_password_empty');
		// Check new password
		if($_POST['new_password'] != $_POST['new_password_confirm'])
			$errors['new_password'] = l('error_password_nomatch');
		
		// If no errors, update the user info.
		if(!count($errors))
		{
			// If the new_password field is filled out,
			// update the users password.
			if(!empty($_POST['new_password']))
				$password = ", password='".$db->res(sha1($_POST['email']))."'";
			
			($hook = FishHook::hook('handler_usercp_save')) ? eval($hook) : false;
			
			$db->query("UPDATE ".DBPF."users SET
			email='".$db->res($_POST['email'])."'
			$password
			WHERE id='".$user->info['id']."' LIMIT 1");
			
			header("Location: ".$uri->geturi()."?updated");
		}
	}
	
	// Fetch user ticket statistics
	$tickets['opened'] = $db->numrows($db->query("SELECT id FROM ".DBPF."tickets WHERE user_id='".$user->info['id']."'"));
	$tickets['updates'] = $db->numrows($db->query("SELECT id FROM ".DBPF."ticket_history WHERE user_id='".$user->info['id']."'"));
	
	// Fetch assigned tickets
	$tickets['assigned'] = array();
	$fetchassigned = $db->query("SELECT * FROM ".DBPF."tickets WHERE assigned_to='".$user->info['id']."' ORDER BY severity");
	while($info = $db->fetcharray($fetchassigned))
	{
		$info['project'] = $db->queryfirst("SELECT slug FROM ".DBPF."projects WHERE id='".$info['project_id']."' LIMIT 1");
		$tickets['assigned'][] = $info;
	}
	
	($hook = FishHook::hook('handler_usercp')) ? eval($hook) : false;
	
	include(template('user/usercp'));
}
?>