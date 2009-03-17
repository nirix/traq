<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array('UserCP'))?></title>
<? include(template('headerinc')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<div id="mainnav" class="nav">
		<ul>
			<li class="first"><a href="<?=$uri->anchor()?>">Projects</a></li>
			<li class="last"><a href="http://rainbirdstudios.com/projects/traq/">Traq</a></li>
		</ul>
	</div>
	<div id="content">
		<h1>UserCP</h1>
		<form action="<?=$uri->anchor('user','settings')?>" method="post">
			<input type="hidden" name="action" value="update" />
			<div class="form">
				<? if(count($errors)) { ?>
				<div class="errormessage">
					<? foreach($errors as $error) { ?>
					<?=$error?><br />
					<? } ?>
				</div><br />
				<? } ?>
				<fieldset<?=(isset($errors['currentpass']) ? ' class="error"' : '')?>>
					<label>Current Password</label>
					<input type="password" name="currentpass" /><br />
					<small>Enter your current password to change your details.</small>
				</fieldset>
				<fieldset<?=(isset($errors['password']) || isset($errors['password2']) ? ' class="error"' : '')?>>
					<label>Password</label>
					<input type="password" name="password" /><br />
					<small>Leave blank to leave as is.</small>
				</fieldset>
				<fieldset<?=(isset($errors['password']) || isset($errors['password2']) ? ' class="error"' : '')?>>
					<label>Password</label>
					<input type="password" name="password2" /> <em>Confirm</em>
				</fieldset>
				<fieldset<?=(isset($errors['email']) ? ' class="error"' : '')?>>
					<label>Email</label>
					<input type="text" name="email" value="<?=$user->info->email?>" />
				</fieldset>
				<fieldset>
					<input type="submit" value="Update" /> <input type="button" onclick="javascript:history.back()" value="Cancel" />
				</fieldset>
			</div>
		</form>
	</div>
<? include(template('footer')); ?>
</body>
</html>