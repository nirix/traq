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
			<li class="first"><a href="<?=$uri->anchor()?>"><?=l('projects')?></a></li>
			<li class="last"><a href="http://rainbirdstudios.com/projects/traq/">Traq</a></li>
		</ul>
	</div>
	<div id="content">
		<h1><?=l('usercp')?></h1>
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
					<label><?=l('current_password')?></label>
					<input type="password" name="currentpass" /><br />
					<small><?=l('enter_current_pass_to_change')?></small>
				</fieldset>
				<fieldset<?=(isset($errors['password']) || isset($errors['password2']) ? ' class="error"' : '')?>>
					<label>Password</label>
					<input type="password" name="password" /><br />
					<small><?=l('leave_blank_to_leave_as_is')?></small>
				</fieldset>
				<fieldset<?=(isset($errors['password']) || isset($errors['password2']) ? ' class="error"' : '')?>>
					<label>Password</label>
					<input type="password" name="password2" /> <em><?=l('confirm')?></em>
				</fieldset>
				<fieldset<?=(isset($errors['email']) ? ' class="error"' : '')?>>
					<label><?=l('email')?></label>
					<input type="text" name="email" value="<?=$user->info->email?>" />
				</fieldset>
				<fieldset>
					<input type="submit" value="<?=l('update')?>" /> <input type="button" onclick="javascript:history.back()" value="<?=l('cancel')?>" />
				</fieldset>
			</div>
		</form>
	</div>
<? include(template('footer')); ?>
</body>
</html>