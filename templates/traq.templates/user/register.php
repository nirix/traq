<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo l('register')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<h1><?php echo l('register')?></h1>
		
		<form action="<?php echo $uri->anchor('user','register')?>" method="post">
			<input type="hidden" name="action" value="register" />
			<div class="form register">
				<?php if($user->errors) { ?>
				<div class="message error">
				<?php foreach($user->errors as $error) { ?>
					<?php echo $error?><br />
				<?php } ?>
				</div>
				<?php } ?>
				<fieldset>
					<label><?php echo l('username')?></label>
					<input type="text" name="username" />
				</fieldset>
				<fieldset>
					<label><?php echo l('full_name')?></label>
					<input type="text" name="name" />
				</fieldset>
				<fieldset>
					<label><?php echo l('password')?></label>
					<input type="password" name="password" />
				</fieldset>
				<fieldset>
					<label><?php echo l('password')?></label>
					<input type="password" name="password2" /> <em><?php echo l('confirm')?></em>
				</fieldset>
				<fieldset>
					<label><?php echo l('email')?></label>
					<input type="text" name="email" />
				</fieldset>
				<?php if(settings('recaptcha_enabled')) { ?>
				<fieldset>
					<legend><?php echo l('recaptcha')?></legend>
					<?php echo recaptcha_get_html(settings('recaptcha_pubkey'), $recaptcha_error)?>
				</fieldset>
				<?php } ?>
				<fieldset>
					<input type="submit" value="<?php echo l('register')?>" /> <input type="button" onclick="javascript:history.back()" value="<?php echo l('cancel')?>" />
				</fieldset>
			</div>
		</form>
		
		<?php require(template('footer')); ?>
	</body>
</html>