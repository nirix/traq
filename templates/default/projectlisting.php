<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle("Project Listing")?></title>
<? include(template('headerinc')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<div id="mainnav" class="nav">
		<ul>
			<li class="first<?=(!$uri->seg[1] ? ' active' : '')?>"><a href="<?=$uri->anchor()?>"><?=l('projects')?></a></li>
			<li class="last"><a href="http://rainbirdstudios.com/projects/traq/">Traq</a></li>
		</ul>
	</div>
	<div id="content">
		<h1><?=l('projects')?></h1>
		<ul class="projects">
<? foreach($projects as $project) { ?>
			<li class="project">
				<div class="info">
					<h2><a href="<?=$uri->anchor($project['slug'])?>"><?=$project['name']?></a></h2>
					<div class="description">
						<?=$project['desc']?> 
					</div>
				</div>
			</li>
<? } ?> 
		</ul>
	</div>
<? include(template('footer')); ?>
</body>
</html>