<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

include("global.php");

authenticate();

// New User
if(isset($_REQUEST['new']))
{
	// Create the user...
	if($_POST['action'] == 'create')
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
		{
			header("Location: users.php?created");
		}
	}
	
	head(l('new_user'),true,'users');
	?>
	<? if(count($user->errors)) { ?>
	<div class="message error">
		<? foreach($user->errors as $error) { ?>
		<?=$error?><br />
		<? } ?>
	</div>
	<? } ?>
	<form action="users.php?new" method="post">
	<input type="hidden" name="action" value="create" />
	<div class="thead"><?=l('new_user')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('username')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('username_description')?></td>
				<td width="200"><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('password')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('password_description')?></td>
				<td width="200"><input type="password" name="password" value="" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('name')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('name_description')?></td>
				<td width="200"><input type="text" name="name" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('email')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('email_description')?></td>
				<td width="200"><input type="text" name="email" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('group')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('group_description')?></td>
				<td width="200">
					<select name="group_id">
						<?php foreach(getgroups() as $group) { ?>
						<option value="<?=$group['id']?>"<?=iif($group['id']==2,' selected="selected"')?>><?=$group['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?=l('create')?>" /></div>
	</div>
	</form>
	<?php
	foot();
}
// Edit user
elseif(isset($_REQUEST['edit']))
{
	// Update user
	if($_POST['action'] == 'save')
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
	<? if(count($errors)) { ?>
	<div class="message error">
		<? foreach($errors as $error) { ?>
		<?=$error?><br />
		<? } ?>
	</div>
	<? } ?>
	<form action="users.php?edit=<?=$_REQUEST['edit']?>" method="post">
	<input type="hidden" name="action" value="save" />
	<div class="thead"><?=l('edit_user')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?=l('username')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('username_description')?></td>
				<td width="200"><input type="text" name="username" value="<?=$user['username']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('password')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('password_description')?></td>
				<td width="200"><input type="password" name="password" value="" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('name')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('name_description')?></td>
				<td width="200"><input type="text" name="name" value="<?=$user['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('email')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('email_description')?></td>
				<td width="200"><input type="text" name="email" value="<?=$user['email']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?=l('group')?></td>
			</tr>
			<tr class="<?=altbg()?>">
				<td><?=l('group_description')?></td>
				<td width="200">
					<select name="group_id">
						<?php foreach(getgroups() as $group) { ?>
						<option value="<?=$group['id']?>"<?=iif($group['id']==$user['group_id'],' selected="selected"')?>><?=$group['name']?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		</table>
		<div class="tfoot" align="center"><input type="submit" value="<?=l('update')?>" /></div>
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
	<div class="thead"><?=l('users')?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr class="optiontitle first">
				<th width="200" align="left"><?=l('username')?></th>
				<th></th>
			</tr>
			<?php
			$fetchusers = $db->query("SELECT id,username FROM ".DBPF."users ORDER BY username ASC");
			while($usr = $db->fetcharray($fetchusers))
			{
			?>
			<tr class="<?=altbg()?>">
				<td><a href="users.php?edit=<?=$usr['id']?>"><?=$usr['username']?></a></td>
				<td align="right">
					<a href="users.php?edit=<?=$usr['id']?>"><img src="images/pencil.png" alt="<?=l('edit')?>" title="<?=l('edit')?>" /></a>
					<a href="#" onclick="if(confirm('<?=l('confirm_delete')?>')) { window.location = 'users.php?delete=<?=$usr['id']?>'; } return false;"><img src="images/delete.png" alt="<?=l('delete')?>" title="<?=l('delete')?>" /></a>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	foot();
}
?>