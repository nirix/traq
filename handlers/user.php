<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

// User Handler
if($uri->seg[1] == "login") {
	// Login
	if(!isset($_POST['action'])) {
		include(template('login'));
	} else if($_POST['action'] == "login") {
		$login = $origin->user->login($_POST['username'],$_POST['password'],$_POST['remember']);
		if($login) {
			header("Location: ".$uri->anchor());
		} else {
			$error = 1;
			include(template('login')); // Fetch the login template
		}
	}
} elseif($uri->seg[1] == "register") {
	// Register
	if(!isset($_POST['action'])) {
		include(template('register')); // Fetch the register template
	} elseif($_POST['action'] == "register") {
		// Error Checking
		$errors = array();
		// Check if Username is empty
		if(empty($_POST['username'])) {
			$errors['username'] = "You must enter a Username";
		}
		// Check if Username is taken or not
		if($db->numrows($db->query("SELECT username FROM ".DBPREFIX."users WHERE username='".$db->escapestring($_POST['username'])."' LIMIT 1"))) {
			$errors['username'] = "Username is unavailable";
		}
		// Check if Password is empty
		if(empty($_POST['password'])) {
			$errors['password'] = "Password cannot be empty";
		}
		// Make sure passwords match
		if($_POST['password'] != $_POST['password2']) {
			$errors['password2'] = "Passwords don't match";
		}
		// Check if email is empty
		if(empty($_POST['email'])) {
			$errors['email'] = "Email cannot be empty";
		}
		
		// If no errors, register the user, or display the register page again
		if(!count($errors)) {
			$data = $_POST;
			$data['groupid'] = '3';
			$user->register($data);
			include(template('register_complete')); // Fetch the register complete template
		} else {
			include(template('register')); // Fetch the register template
		}
	}
} elseif($uri->seg[1] == "logout") {
	// Logout
	$user->logout();
	header("Location: ".$uri->anchor()); // Redirect to the main page
} else if($uri->seg[1] == "settings") {
	// User CP/Settings
	if($_POST['action'] == "update") {
		// Update the user info
		
		// Check for errors
		$errors = array();
		// Check if current password is valid
		if(sha1($_POST['currentpass']) != $user->info->password) {
			$errors['currentpass'] = "Invalid current password.";
		}
		// Check if passwords exist
		if($_POST['password'] != $_POST['password2']) {
			$errors['password2'] = "Passwords do not match";
		}
		// Check if email is blank
		if(empty($_POST['email'])) {
			$errors['email'] = "You must enter an Email";
		}
		
		// If there are no errors, update the user info, else show usercp page again
		if(!count($errors)) {
			if(!empty($_POST['password'])) {
				$password = "password='".sha1($_POST['password'])."',";// SHA1 the password
			}
			$db->query("UPDATE ".DBPREFIX."users SET $password email='".$db->escapestring($_POST['email'])."' WHERE id='".$user->info->id."' LIMIT 1");
			header("Location: ".$uri->anchor('user','settings')); // Redirect to the user cp/settings page
		} else {
			include(template('usercp')); // Fetch the usercp template
		}
	} else {
		include(template('usercp')); // Fetch the usercp template
	}
}
?>