<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body id="project_info">
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<h1><?php echo $project['name']?></h1>
		<?php if($user->loggedin) { ?>
		<div><a href="<?php echo $uri->anchor($project['slug'],'watch')?>"><?php echo l(iif(is_subscribed('project',$project['id']),'Unwatch','Watch').'_this_project')?></a></div>
		<?php } ?>
		<p>
			<?php echo formattext($project['info'])?>
		</p>
		
		<div id="charts">
			<h3><?php echo l('charts')?></h3>
			<img src="http://chart.apis.google.com/chart?chs=300x150&cht=p3&chco=7777CC|76A4FB|3399CC|3366CC&chds=0,<?php echo $tickets['open']+$tickets['closed']?>&chd=t:<?php echo $tickets['open'].','.$tickets['closed']?>&chdl=<?php echo l('open').'|'.l('closed'); ?>&chtt=<?php echo l('tickets')?>&chts=676767,12" width="300" height="150" alt="" />
			<img src="http://chart.apis.google.com/chart?chs=300x150&cht=p3&chco=7777CC|76A4FB|3399CC|3366CC&chds=0,<?php echo $milestones['open']+$milestones['completed']?>&chd=t:<?php echo $milestones['open'].','.$milestones['completed']?>&chdl=<?php echo l('open').'|'.l('completed'); ?>&chtt=<?php echo l('milestones')?>&chts=676767,12" width="300" height="150" alt="" />
		</div>
		
		<?php require(template('footer')); ?>
	</body>
</html>