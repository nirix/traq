<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=l('register')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<h1><?=l('register')?></h1>
		
		<? if($user->errors) { ?>
		<div class="message error">
		<? foreach($user->errors as $error) { ?>
			<?=$error?><br />
		<? } ?>
		</div>
		<? } ?>
		<form action="<?=$uri->anchor('user','register')?>" method="post">
			<input type="hidden" name="action" value="register" />
			<div class="form register">
				<fieldset>
					<label><?=l('username')?></label>
					<input type="text" name="username" />
				</fieldset>
				<fieldset>
					<label><?=l('full_name')?></label>
					<input type="text" name="name" />
				</fieldset>
				<fieldset>
					<label><?=l('password')?></label>
					<input type="password" name="password" />
				</fieldset>
				<fieldset>
					<label><?=l('password')?></label>
					<input type="password" name="password2" /> <em><?=l('confirm')?></em>
				</fieldset>
				<fieldset>
					<label><?=l('email')?></label>
					<input type="text" name="email" />
				</fieldset>
				<? if(settings('recaptcha_enabled')) { ?>
				<fieldset>
					<legend><?=l('recaptcha')?></legend>
					<?=recaptcha_get_html(settings('recaptcha_pubkey'), $recaptcha_error)?>
				</fieldset>
				<? } ?>
				<fieldset>
					<input type="submit" value="<?=l('register')?>" /> <input type="button" onclick="javascript:history.back()" value="<?=l('cancel')?>" />
				</fieldset>
			</div>
		</form>
		
		<? require(template('footer')); ?>
	</body>
</html>