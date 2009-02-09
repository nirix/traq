<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array('Timeline',$project['name']))?></title>
<? include(template('style')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<h1><?=$project['name']?> Timeline</h1>
		<ul class="timeline">
		<? foreach($dates as $date) { ?>
			<li class="date"><h2><?=date("d/m/Y",$date['timestamp'])?></h2>
				<ul class="rows">
				<? foreach($date['rows'] as $row) { ?>
					<? if($row['type'] == "TICKETCREATE") { ?>
					<li><?=date("g:iA",$row['timestamp'])?> Ticket <a href="<?=$uri->anchor($project['slug'],'ticket',$row['ticket']['tid'])?>">#<?=$row['ticket']['tid']?></a> (<?=tickettype($row['ticket']['type'])?>) created</li>
					<? } ?>
				<? } ?>
				<ul>
			</li>
		</ul>
		<? } ?>
	</div>
<? include(template('footer')); ?>
</body>
</html>