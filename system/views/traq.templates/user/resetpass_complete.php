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
		
		<p align="center"><?php echo l('new_password_is_x_login_and_change_asap', $newpass); ?></p>
		
		<?php require(template('footer')); ?>
	</body>
</html>