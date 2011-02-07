<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo l('timeline')?></title>
		<link rel="alternate" type="application/rss+xml" title="Timeline (RSS 2.0)" href="http://<?php echo $_SERVER['HTTP_HOST'].$uri->anchor($project['slug'],'feeds','timeline')?>" />
		<?php require(template('headerinc')); ?>
	</head>
	<body id="timeline">
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<h1><?php echo l('timeline')?></h1>
		<p><?php echo l('show')?>: <a href="?days=7"><?php echo l('1_week')?></a>, <a href="?days=14"><?php echo l('2_weeks')?></a>, <a href="?days=28"><?php echo l('4_weeks')?></a>, <a href="?days=366"><?php echo l('1_year')?></a>, <a href="?days=all"><?php echo l('All_time')?></a></p>
		
		<?php foreach($days as $day) { ?>
		<div class="day">
			<h3><?php echo date("l, jS F Y",$day['timestamp'])?></h3>
			<ul>
			<?php foreach($day['changes'] as $change) { ?>
				<li><?php echo date("h:iA",$change['timestamp'])?> <a href="<?php echo $change['url']?>"><?php echo $change['text']?></a></li>
			<?php } ?>
			</ul>
		</div>
		<?php } ?>
		
		<?php require(template('footer')); ?>
	</body>
</html>