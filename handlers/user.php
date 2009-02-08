<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
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
			include("templates/login.php");
		}
	}
} elseif($uri->seg[1] == "register") {
	// Register
	if(!isset($_POST['action'])) {
		include(template('register'));
	} elseif($_POST['action'] == "register") {
		// Field Checking
		$errors = array();
		if(empty($_POST['username'])) {
			$errors['username'] = "You must enter a Username";
		}
		if($db->numrows($db->query("SELECT username FROM ".DBPREFIX."users WHERE username='".$db->escapestring($_POST['username'])."' LIMIT 1"))) {
			$errors['username'] = "Username is unavailable";
		}
		if(empty($_POST['password'])) {
			$errors['password'] = "Password cannot be empty";
		}
		if($_POST['password'] != $_POST['password2']) {
			$errors['password2'] = "Passwords don't match";
		}
		if(empty($_POST['email'])) {
			$errors['email'] = "Email cannot be empty";
		}
		
		if(!count($errors)) {
			$data = $_POST;
			$data['groupid'] = '3';
			$user->register($data);
			include(template('register_complete'));
		} else {
			include(template('register'));
		}
	}
} elseif($uri->seg[1] == "logout") {
	$user->logout();
	header("Location: ".$uri->anchor());
} else if($uri->seg[1] == "settings") {
	if($_POST['action'] == "update") {
		if(sha1($_POST['currentpass']) != $user->info->password) {
			$errors['currentpass'] = "Invalid current password.";
		}
		if($_POST['password'] != $_POST['password2']) {
			$errors['password2'] = "Passwords do not match";
		}
		if(empty($_POST['email'])) {
			$errors['email'] = "You must enter an Email";
		}
		if(!count($errors)) {
			if(!empty($_POST['password'])) {
				$password = "password='".sha1($_POST['password'])."',";
			}
			$db->query("UPDATE ".DBPREFIX."users SET $password email='".$db->escapestring($_POST['email'])."' WHERE uid='".$user->info->uid."' LIMIT 1");
			header("Location: ".$uri->anchor('user','settings'));
		} else {
			include(template('usercp'));
		}
	} else {
		include(template('usercp'));
	}
}
?>