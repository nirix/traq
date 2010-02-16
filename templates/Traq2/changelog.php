<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=$project['name']?> / <?=l('changelog')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body id="changelog">
		<? require(template('header')); ?>
		
		<? require(template('breadcrumbs')); ?>
		
		<fieldset class="legend">
			<legend>Legend</legend>
			<? foreach($types as $type) { ?>
			<span class="type_bullet"><?=$type['bullet']?></span> = <?=$type['name']?><br />
			<? } ?>
		</fieldset>
		
		<? foreach($milestones as $milestone) { ?>
		<h3><?=l('milestone_x',$milestone['milestone'])?></h3>
		<div class="changes">
			<? foreach($milestone['changes'] as $change) { ?>
			<span class="type_bullet"><?=$types[$change['type']]['bullet']?></span> <?=$change['summary']?><br />
			<? } ?>
		</div>
		<br />
		<? } ?>
		
		<? require(template('footer')); ?>
	</body>
</html>