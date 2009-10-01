<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 */

if($uri->seg[1] == 'login')
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
elseif($uri->seg[1] == 'register')
{
	if($_POST['action'] == 'register')
	{
		if($user->register($data))
		{
			header("Location: ".$uri->anchor('user','settings')."?welcome");
		}
	}
	include(template('user/register'));
}
?>