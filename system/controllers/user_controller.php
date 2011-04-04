<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
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
 */

class UserController extends AppController
{
	public function login()
	{
		if(Request::$method == 'post')
		{
			if($this->db->select(array('id'))->from('users')->where(array('username'=>rescape(Param::$post['username']),'password'=>sha1(Param::$post['password'])))->exec()->numRows())
			{
				$sesshash = sha1(Param::$post['username'].time().rand(0,9999).time().date('r',time()));
				
				setcookie('traqsess', $sesshash, (Param::$post['remember_me'] ? time()+99999999 : 0), '/');
				$this->db->query("UPDATE ".DBPREFIX."users SET sesshash='".$sesshash."' WHERE username='".rescape(Param::$post['username'])."' AND password='".sha1(Param::$post['password'])."' LIMIT 1");
				header("Location: ".(isset(Param::$post['redir']) ? Param::$post['redir'] : baseurl()));
			}
		}
	}
}