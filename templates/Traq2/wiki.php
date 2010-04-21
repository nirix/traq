<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body id="project_info">
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<h1><?php echo $wiki['title']?></h1>
		<?php echo formattext($wiki['body']); ?>
		
		<?php require(template('footer')); ?>
	</body>
</html>