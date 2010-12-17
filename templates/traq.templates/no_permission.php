<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body id="timeline">
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<div id="no_permission">
			<h1><?php echo l('error')?></h1>
			<?php echo l('error_no_permission')?>
		</div>
		
		<?php require(template('footer')); ?>
	</body>
</html>