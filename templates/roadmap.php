<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array('Roadmap',$project['name']))?></title>
<? include(template('style')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<h1><?=$project['name']?> Roadmap</h1>
		<ul class="milestones">
<? foreach($milestones as $milestone) { ?>
			<li class="milestone">
				<div class="info">
					<h2><a href="<?=$uri->anchor($project['slug'],'milestone',$milestone['milestone'])?>">Milestone: <em><?=$milestone['milestone']?><?=(!empty($milestone['codename']) ? ' <small>"'.$milestone['codename'].'"</small>' : '')?></em></a></h2>
					<dl>
						<dt>Closed tickets:</dt>
						<dd><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'],'closed')?>"><?=$milestone['tickets']['closed']?></a></dd>
						<dt>Active tickets:</dt>
						<dd><a href="<?=$uri->anchor($project['slug'],'tickets',$milestone['milestone'],'open')?>"><?=$milestone['tickets']['open']?></a></dd>
					</dl>
					<div class="description">
						<?=$milestone['desc']?> 
					</div>
				</div>
			</li>
<? } ?> 
		</ul>
	</div>
<? include(template('footer')); ?>
</body>
</html>