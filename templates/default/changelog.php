<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array('Change Log',$project['name']))?></title>
<? include(template('headerinc')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<h1><?=l('x_change_log',$project['name'])?></h1>
		<div id="changelog">
		<? foreach($milestones as $milestone) {
			if(!count($milestone['tickets'])) {
				continue;	
			}
		?>
			<h2><?=l('milestone_x',$milestone['milestone'])?></h2>
			<? foreach($milestone['tickets'] as $ticket) { ?>
			<div class="ticket">- <a href="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>"><?=l('ticket_x_closed_x',$ticket['tid'],$ticket['summary'],ticketstatus($ticket['status']))?></a></div>
			<? } ?>
		<? } ?>
		</div>
	</div>
<? include(template('footer')); ?>
</body>
</html>