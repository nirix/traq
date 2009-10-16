<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 */

if($uri->seg[1] == "login")
{
	if($_POST['action'] == 'login')
	{
		if($user->login($_POST['username'],$_POST['password'],$_POST['remember']))
		{
			header("Location: ".$uri->anchor());
		}
	}
	include(template('user/login'));
}
elseif($uri->seg[1] == "register")
{
	if($_POST['action'] == 'register')
	{
		$data = array(
			'username' => $_POST['username'],
			'password' => $_POST['password'],
			'password2' => $_POST['password2'],
			'email' => $_POST['email'],
			'name' => $_POST['name']
		);
		if($user->register($data))
		{
			header("Location: ".$uri->anchor('user','settings')."?welcome");
		}
	}
	include(template('user/register'));
}
elseif($uri->seg[1] == "logout")
{
	$user->logout();
	header("Location: ".$uri->anchor());
}
elseif($uri->seg[1] == "settings")
{
	include(template('user/settings'));
}
?>