<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=l('login')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<h1><?=l('login')?></h1>
		
		<? if($user->errors) { ?>
		<div class="message error">
		<? foreach($user->errors as $error) { ?>
			<?=$error?><br />
		<? } ?>
		</div>
		<? } ?>
		<form action="<?=$uri->anchor('user','login')?>" method="post">
			<input type="hidden" name="action" value="login" />
			<input type="hidden" name="goto" value="<?=$_REQUEST['goto']?>" />
			<div class="form login">
				<fieldset>
					<label><?=l('username')?></label>
					<input type="text" name="username" />
				</fieldset>
				<fieldset>
					<label><?=l('password')?></label>
					<input type="password" name="password" />
				</fieldset>
				<fieldset>
					<label for="remember"><?=l('remember')?></label> <input type="checkbox" name="remember" value="1" id="remember" />
					<input type="submit" value="<?=l('login')?>" /> <input type="button" onclick="javascript:history.back()" value="<?=l('cancel')?>" />
				</fieldset>
			</div>
		</form>
		
		<? require(template('footer')); ?>
	</body>
</html>