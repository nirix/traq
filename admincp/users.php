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

authenticate();

// New User
if(isset($_REQUEST['new']))
{
	// Create the user...
	if(@$_POST['action'] == 'create')
	{
		// User data array.
		$data = array(
			'username' => $_POST['username'],
			'password' => $_POST['password'],
			'password2' => $_POST['password'],
			'email' => $_POST['email'],
			'name' => $_POST['name'],
			'group_id' => $_POST['group_id']
		);
		
		if($user->register($data))
			header("Location: users.php?created");
	}
	
	head(l('new_user'),true,'users');
	?>
	<?php if(count($user->errors)) { ?>
	<div class="message error">
		<?php foreach($user->errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="users.php?new" method="post">
	<input type="hidden" name="action" value="create" />
	<div class="thead"><?php echo l('new_user')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('username')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('username_description')?></td>
				<td width="200"><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('password')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('password_description')?></td>
				<td width="200"><input type="password" name="password" value="" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('name_description')?></td>
				<td width="200"><input type="text" name="name" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('email')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('email_description')?></td>
				<td width="200"><input type="text" name="email" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('group')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('group_description')?></td>
				<td width="200">
					<select name="group_id">
						<?php foreach(getgroups() as $group) { ?>
						<option value="<?php echo $group['id']?>"<?php echo iif($group['id']==2,' selected="selected"')?>><?php echo $group['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('create')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Edit user
elseif(isset($_REQUEST['edit']))
{
	$errors = array();
	
	// Update user
	if(isset($_POST['action']) and $_POST['action'] == 'save')
	{
		// Check for errors...
		// Check if username is entered
		if(empty($_POST['username']))
			$errors['username'] = l('error_username_empty');
		// Check if username is taken.
		if($db->numrows($db->query("SELECT id FROM ".DBPF."users WHERE username='".$db->res($_POST['username'])."' AND id!='".$db->res($_REQUEST['edit'])."' LIMIT 1")))
			$errors['username'] = l('error_username_taken');
		// Check if email is entered
		if(empty($_POST['email']))
			$errors['email'] = l('error_email_empty');
		
		// If no errors, update the users info.
		if(!count($errors))
		{
			$db->query("UPDATE ".DBPF."users SET
			username='".$db->res($_POST['username'])."',
			".(!empty($_POST['password']) ? "password='".sha1($_POST['password'])."'," :'')."
			name='".$db->res($_POST['name'])."',
			email='".$db->res($_POST['email'])."',
			group_id='".$db->res($_POST['group_id'])."'
			WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
			
			header("Location: users.php");
		}
	}
	
	// Fetch user info
	$user = $db->queryfirst("SELECT * FROM ".DBPF."users WHERE id='".$db->res($_REQUEST['edit'])."' LIMIT 1");
	
	head(l('edit_user'),true,'users');
	?>
	<?php if(count($errors)) { ?>
	<div class="message error">
		<?php foreach($errors as $error) { ?>
		<?php echo $error?><br />
		<?php } ?>
	</div>
	<?php } ?>
	<form action="users.php?edit=<?php echo $_REQUEST['edit']?>" method="post">
	<input type="hidden" name="action" value="save" />
	<div class="thead"><?php echo l('edit_user')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('username')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('username_description')?></td>
				<td width="200"><input type="text" name="username" value="<?php echo $user['username']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('password')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('password_description')?></td>
				<td width="200"><input type="password" name="password" value="" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('name')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?php echo $user['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('email')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('email_description')?></td>
				<td width="200"><input type="text" name="email" value="<?php echo $user['email']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('group')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td><?php echo l('group_description')?></td>
				<td width="200">
					<select name="group_id">
						<?php foreach(getgroups() as $group) { ?>
						<option value="<?php echo $group['id']?>"<?php echo iif($group['id']==$user['group_id'],' selected="selected"')?>><?php echo $group['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Delete user
elseif(isset($_REQUEST['delete']))
{
	$db->query("DELETE FROM ".DBPF."users WHERE id='".$db->res($_REQUEST['delete'])."' LIMIT 1");
	header("Location: users.php?deleted");
}
// List users
else
{
	head(l('users'),true,'users');
	?>
	<div class="thead"><?php echo l('users')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?php echo l('username')?></th>
				<th></th>
			</tr>
			<?php
			$fetchusers = $db->query("SELECT id,username FROM ".DBPF."users ORDER BY username ASC");
			while($usr = $db->fetcharray($fetchusers))
			{
			?>
			<tr class="<?php echo altbg()?>">
				<td><a href="users.php?edit=<?php echo $usr['id']?>"><?php echo $usr['username']?></a></td>
				<td align="right">
					<a href="users.php?edit=<?php echo $usr['id']?>"><img src="images/pencil.png" alt="<?php echo l('edit')?>" title="<?php echo l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$usr['name'])?>')) { window.location = 'users.php?delete=<?php echo $usr['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	foot();
}
?>