<?php
/**
 * Traq 2
 * Copyright (c) 2009
 * All Rights Reserved
 *
 * $Id: index.php 2010-04-07 19:28:16Z pierre $
 */

$return = false;
if(isset($_POST['savedetails']) && !empty($_POST['savedetails']))
{
	// Check for errors in the fields.
	if(empty($_POST['traq_name']))
		$error = true;
	if(empty($_POST['admin_name']))
		$error = true;
	if(empty($_POST['admin_pass']))
		$error = true;
	if(empty($_POST['admin_email']))
		$error = true;
	if(empty($error))
	{
		include('../system/config.php');
		include('../system/libraries/db.class.php');
		include('../system/libraries/user.class.php');
		define("DBPF",$conf['db']['prefix']);

		// Connect to the Database.
		$db = new Database($conf['db']['server'],$conf['db']['user'],$conf['db']['pass'],$conf['db']['dbname']);

		// Insert Settings.
		$db->query("UPDATE ".$conf['db']['prefix']."settings SET value='".$db->res($_POST['traq_name'])."' WHERE setting='title'");

		// Create Admin User.
		$user = new User;
		$admindata = array(
			'username' => $_POST['admin_name'],
			'password' => $_POST['admin_pass'],
			'password2' => $_POST['admin_pass'],
			'email' => $_POST['admin_email'],
			'name' => $_POST['admin_name']
		);
		$user->register($admindata);
		$db->query("UPDATE ".$conf['db']['prefix']."users SET group_id='1' WHERE username='".$db->res($_POST['admin_name'])."' LIMIT 1");
		
		$return = true;
		?>
		<table width="55%" align="center" style="margin-left:20%" cellpadding="4" cellspacing="4">
			<tr>
				<td align="center" class="good"><h2>Installation Complete</h2>You may now login to the <a href="../admincp/">AdminCP</a> with the username and password you provided.</td>
			</tr>
		</table>
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
	<?php
	} else {
		echo '<table width="400" align="center">
				<tr>
					<td align="center" class="bad"><h2>Error</h2>Please fill in all fields.</td>
				</tr>
			</table>';
	}
}

if(!$return)
{
?>
	<h4 align="center">System Configuration</h4>
	<form action="index.php" method="post">
		<input type="hidden" name="step" value="<?php echo $step; ?>" />
		
		<table width="100%" align="center" style="margin-left:35%" cellpadding="4" cellspacing="4">
			<tr>
				<td>Traq name</td>
				<td><input type="text" name="traq_name" value="Traq" /></td>
			</tr>
			<tr>
				<td>Admin Username</td>
				<td><input type="text" name="admin_name" value="admin" /></td>
			</tr>
			<tr>
				<td>Admin Password</td>
				<td><input type="password" name="admin_pass" /></td>
			</tr>
			<tr>
				<td>Admin Email</td>
				<td><input type="text" name="admin_email" /></td>
			</tr>
		</table>
		
		<div align="center"><input type="submit" value="Next" /></div>
		<input type="hidden" name="savedetails" value="true" />
	</form>
<?php
}
?>
