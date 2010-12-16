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

include("global.php");

// Login
if(isset($_POST['username']))
{
	// Check if the username and password are correct.
	$login = $user->login($_POST['username'],$_POST['password']);
	if($login)
		header("Location: index.php");
}

define("HIDENAV",true);
head(l('login'));
?>
			<form action="login.php" method="post">
			<div id="login">
				<?php if(!@$login && isset($_POST['username'])) { ?>
				<div class="message error">Invalid Username or Password</div>
				<?php } ?>
				<div class="thead"><?php echo l('login')?></div>
				<div class="tborder" align="center">
					<table width="100% cellspacing="0">
						<tr>
							<td><?php echo l('username')?></td>
							<td><input type="text" name="username" /></td>
						</tr>
						<tr>
							<td><?php echo l('password')?></td>
							<td><input type="password" name="password" /></td>
						</tr>
					</table>
					<div class="tfoot">
						<input type="submit" value="<?php echo l('login')?>" />
					</div>
				</div>
			</div>
			</form>
<?php
foot();
?>