<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<ul class="projects">
		<? foreach($projects as $project) { ?>
			<li class="project">
				<div class="info">
					<h2><a href="<?=$uri->anchor($project['slug'])?>"><?=$project['name']?></a></h2>
					<div class="description">
						<small class="quick_nav"><a href="<?=$uri->anchor($project['slug'],'roadmap')?>">Roadmap</a> | <a href="<?=$uri->anchor($project['slug'],'tickets')?>">All Tickets</a> | <a href="<?=$uri->anchor($project['slug'],'timeline')?>">Timeline</a></small>
						 <?=$project['info']?>
					</div>
				</div>
			</li>
 		<? } ?>
		</ul>
		
		<? require(template('footer')); ?>
	</body>
</html>