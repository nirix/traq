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

// Login
if($uri->seg(1) == "login")
{
	// Check if the form has been submitted.
	if(isset($_POST['action']) && $_POST['action'] == 'login')
	{
		// Check if there were errors, if not go back to where we came from.
		if($user->login($_POST['username'],$_POST['password'],(isset($_POST['remember']) ? $_POST['remember'] :'')))
			header("Location: ".(isset($_POST['goto']) && $_POST['goto'] != '' ? urldecode(urlencode(($_POST['goto']))) : $uri->anchor()));
	}
	include(template('user/login'));
}
// Register
elseif($uri->seg(1) == "register" && settings('allow_registration'))
{
	// Include reCaptcha
	require(TRAQPATH.'system/libraries/recaptchalib.php');
	
	$errors = array();
	
	// Check if the form has been submitted.
	if(isset($_POST['action']) && $_POST['action'] == 'register')
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
		if(isset($errors) && !count($errors))
		{
			if($user->register($data))
				header("Location: ".$uri->anchor('user','login'));
		}
	}
	$errors = array_merge($errors,$user->errors);
	include(template('user/register'));
}
// Logout
elseif($uri->seg(1) == "logout")
{
	$user->logout();
	header("Location: ".$uri->anchor());
}
elseif($uri->seg(1) == "usercp")
{
	// Update user info
	if(@$_POST['action'] == 'save')
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
				$password = ", password='".$db->res(sha1($_POST['new_password']))."'";
			
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
	$fetchassigned = $db->query("SELECT * FROM ".DBPF."tickets WHERE assigned_to='".$user->info['id']."' AND closed='0' ORDER BY severity");
	while($info = $db->fetcharray($fetchassigned))
	{
		// Fetch slug
		$info['project'] = $db->queryfirst("SELECT slug FROM ".DBPF."projects WHERE id='".$info['project_id']."' LIMIT 1");
		$tickets['assigned'][] = $info;
	}
	
	($hook = FishHook::hook('handler_usercp')) ? eval($hook) : false;
	
	include(template('user/usercp'));
}
// Reset Password
elseif($uri->seg(1) == 'resetpass')
{
	// Reset
	if(isset($_REQUEST['action']) && $_POST['action'] == 'update')
	{
		// Find the user
		$fetchuser = $db->query("SELECT id FROM ".DBPF."users WHERE sesshash='RESET:".$db->res($_POST['hash'])."' LIMIT 1");
		if($db->numrows($fetchuser))
		{
			// Update the users password and rediect to the login page.
			$db->query("UPDATE ".DBPF."users SET password='".sha1($_POST['password'])."' WHERE sesshash='RESET:".$db->res($_POST['hash'])."' LIMIT 1");
			header("Location: ".$uri->anchor('user','login')."?reset");
		}
		$error = l('error_resetting_password');
	}
	
	// Email
	if(isset($_POST['action']) && $_POST['action'] == 'reset')
	{
		// Check the user exists...
		$fetchuser = $db->query("SELECT username,name,email FROM ".DBPF."users WHERE username='".$db->res($_POST['username'])."' LIMIT 1");
		if($db->numrows($fetchuser))
		{
			// Create a unique hash and store it in the
			// users session hash field.
			$reset = sha1(time().date("Y-m-d g:ia").$_POST['username'].rand(1,time()));
			$db->query("UPDATE ".DBPF."users SET sesshash='RESET:".$reset."' WHERE username='".$db->res($_POST['username'])."' LIMIT 1");
			
			// Send the email.
			$userinfo = $db->fetcharray($fetchuser);
			mail($userinfo['email'],l('x_password_reset',settings('title')),l('password_reset_message',$userinfo['name'],$reset),"From: ".settings('title')." <noreply@".$_SERVER['HTTP_HOST'].">");
			$sent = true;
		}
		else
		{
			$error = l('error_user_not_found');
		}
	}
	
	include(template('user/resetpass'));
}
?>