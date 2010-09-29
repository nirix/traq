<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo l('Reset_Password'); ?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<h1><?php echo l('Reset_Password')?></h1>
		
		<?php if(isset($reset) and $reset) { ?>
		<p align="center">Reset instructions have been sent to accounts email address.</p>
		<?php } ?>
		
		<form action="<?php echo $uri->anchor('user','resetpass')?>" method="post">
			<input type="hidden" name="action" value="reset" />
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
					<input type="submit" value="<?php echo l('Reset')?>" /> <input type="button" onclick="javascript:history.back()" value="<?php echo l('cancel')?>" />
				</fieldset>
			</div>
		</form>
		
		<?php require(template('footer')); ?>
	</body>
</html>