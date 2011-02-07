<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo l('login')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<h1><?php echo l('login')?></h1>
		
		<form action="<?php echo $uri->anchor('user','login')?>" method="post">
			<input type="hidden" name="action" value="login" />
			<input type="hidden" name="goto" value="<?php echo (isset($_REQUEST['goto']) ? $_REQUEST['goto'] :'')?>" />
			<div class="form login">
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
					<label><?php echo l('password')?></label>
					<input type="password" name="password" />
				</fieldset>
				<fieldset>
					<label for="remember"><?php echo l('remember')?></label> <input type="checkbox" name="remember" value="1" id="remember" />
					<input type="submit" value="<?php echo l('login')?>" /> <input type="button" onclick="javascript:history.back()" value="<?php echo l('cancel')?>" />
				</fieldset>
				<div align="center"><a href="<?php echo $uri->anchor('user','resetpass')?>"><?php echo l('Reset_Password')?></a></div>
			</div>
		</form>
		
		<?php require(template('footer')); ?>
	</body>
</html>