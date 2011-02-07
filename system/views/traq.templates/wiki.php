<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo $wiki['title']?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body id="project_info">
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<h1><?php echo $wiki['title']?> <?php if($user->group['is_admin']) {?> <img id="edit_wiki_page" src="<?php echo baseurl(); ?>admincp/images/pencil.png" alt="<?php echo l('edit'); ?>" /><?php } ?></h1>
		<input type="hidden" id="wikident" value="<?php echo $wiki['id']; ?>" />
		<div id="wiki_page">
			<?php echo formattext($wiki['body']); ?>
		</div>
		
		<?php require(template('footer')); ?>
	</body>
</html>