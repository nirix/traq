<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo l('changelog')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body id="changelog">
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<fieldset class="legend">
			<legend>Legend</legend>
			<?php foreach($types as $type) { ?>
			<span class="type_bullet"><?php echo $type['bullet']?></span> = <?php echo $type['name']?><br />
			<?php } ?>
		</fieldset>
		
		<?php foreach($milestones as $milestone) { ?>
		<h3><?php echo l('milestone_x',$milestone['milestone'])?></h3>
		<div class="changes">
			<?php foreach($milestone['changes'] as $change) { ?>
			<span class="type_bullet"><?php echo $types[$change['type']]['bullet']?></span> <?php echo $change['summary']?><br />
			<?php } ?>
			<?php echo $milestone['changelog']?>
		</div>
		<br />
		<?php } ?>
		
		<?php require(template('footer')); ?>
	</body>
</html>