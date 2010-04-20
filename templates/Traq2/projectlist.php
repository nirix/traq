<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<ul class="projects">
		<?php foreach($projects as $project) { ?>
			<li class="project">
				<div class="info">
					<h2><a href="<?php echo $uri->anchor($project['slug'])?>"><?php echo $project['name']?></a></h2>
					<div class="description">
						<small class="quick_nav"><a href="<?php echo $uri->anchor($project['slug'],'roadmap')?>">Roadmap</a> | <a href="<?php echo $uri->anchor($project['slug'],'tickets')?>">All Tickets</a> | <a href="<?php echo $uri->anchor($project['slug'],'timeline')?>">Timeline</a> <?php ($hook = FishHook::hook('template_projectlist_quick_nav')) ? eval($hook) : false; ?></small>
						<?php echo formattext($project['info'])?>
					</div>
				</div>
			</li>
 		<?php } ?>
		</ul>
		
		<?php require(template('footer')); ?>
	</body>
</html>