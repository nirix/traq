<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=$project['name']?> / <?=l('timeline')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body id="timeline">
		<? require(template('header')); ?>
		
		<? require(template('breadcrumbs')); ?>
		
		<? foreach($days as $day) { ?>
		<div class="day">
			<h3><?=date("l, jS F Y",$day['timestamp'])?></h3>
			<ul>
			<? foreach($day['changes'] as $change) { ?>
				<li><?=date("h:iA",$change['timestamp'])?> <a href="<?=$change['url']?>"><?=$change['text']?></a></li>
			<? } ?>
			</ul>
		</div>
		<? } ?>
		
		<? require(template('footer')); ?>
	</body>
</html>