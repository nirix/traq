<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=$project['name']?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body id="project_info">
		<? require(template('header')); ?>
		
		<? require(template('breadcrumbs')); ?>
		
		<h1><?=$project['name']?></h1>
		<p>
			<?=$project['info']?>
		</p>
		
		<? require(template('footer')); ?>
	</body>
</html>