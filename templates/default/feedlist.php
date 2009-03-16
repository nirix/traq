<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array('Feeds List',$project['name']))?></title>
<? include(template('style')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<h1><?=$project['name']?> Feeds List</h1>
		<ul class="feedslist">
			<li><a href="<?=$uri->anchor($project['slug'],'feeds','timeline','rss2')?>">Timeline RSS Feed</a></li>
		</ul>
	</div>
<? include(template('footer')); ?>
</body>
</html>