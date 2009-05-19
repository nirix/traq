<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array(l('timeline'),$project['name']))?></title>
<? include(template('headerinc')); ?> 
<link rel="alternate" type="application/rss+xml" title="<?=l('x_timeline_rss_feed',$project['name'])?>" href="http://<?=$_SERVER['HTTP_HOST']?><?=$uri->anchor($project['slug'],'feeds','timeline','rss2')?>" />
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<h1><?=l('x_timeline',$project['name'])?></h1>
		<ul class="timeline">
		<? foreach($dates as $date) { ?>
			<li class="date">
				<h2><?=date("D jS F Y",$date['timestamp'])?></h2>
				<ul class="rows">
				<? foreach($date['rows'] as $row) { ?>
					<? if($row['type'] == "TICKETCREATE") { ?>
					<li><?=date("h:iA",$row['timestamp'])?> <a href="<?=$uri->anchor($project['slug'],'ticket',$row['ticket']['tid'])?>"><?=l('ticket_x_created_by_x',$row['ticket']['tid'],$row['ticket']['summary'],$row['username'])?></a></li>
					<? } elseif($row['type'] == "TICKETCLOSE") { ?>
					<li><?=date("h:iA",$row['timestamp'])?> <a href="<?=$uri->anchor($project['slug'],'ticket',$row['ticket']['tid'])?>"><?=l('ticket_x_closed_by_x',$row['ticket']['tid'],$row['ticket']['summary'],$row['username'])?></a></li>
					<? } elseif($row['type'] == "TICKETREOPEN") { ?>
					<li><?=date("h:iA",$row['timestamp'])?> <a href="<?=$uri->anchor($project['slug'],'ticket',$row['ticket']['tid'])?>"><?=l('ticket_x_reopened_by_x',$row['ticket']['tid'],$row['ticket']['summary'],$row['username'])?></a></li>
					<? } ?>
				<? } ?>
				</ul>
			</li>
		<? } ?>
		</ul>
	</div>
<? include(template('footer')); ?>
</body>
</html>