<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
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
	{
		header("Location: index.php");
	}
}

define("HIDENAV",true);
head(l('login'));
?>
			<form action="login.php" method="post">
			<div id="login">
				<?php if(!$login && isset($_POST['username'])) { ?>
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