<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array($ticket['summary']. ' (ticket #'.$ticket['id'].')','Tickets',$project['name']))?></title>
<? include(template('style')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<h1><?=$project['name']?>: <?=$ticket['summary']?> <small>(ticket #<?=$ticket['id']?>)</small></h1>
		<div id="ticket">
			<div class="date">
				<p title="12/14/08 08:37:54">Opened <?=timesince($ticket['timestamp'])?> ago</p>
				<p title="01/19/09 14:13:28">Last modified <?=($ticket['updated'] ? timesince($ticket['updated']).' ago' : 'Never')?></p>
			</div>
			<h2 class="summary"><?=$ticket['summary']?> <small>(#<?=$ticket['id']?>)</small></h2>
			<table class="properties">
				<tr>
					<th id="h_owner">Reported by:</th>
					<td headers="h_owner"><?=$owner['username']?></td>
					<th id="h_assignee">Assigned to:</th>
					<td headers="h_assignee"><?=$assignee['username']?></td>
				</tr>
				<tr>
					<th id="h_priority">Priority:</th>
					<td headers="h_priority"><?=ticketpriority($ticket['priority'])?></td>
					<th id="h_milestone">Milestone:</th>
					<td headers="h_milestone"><?=$milestone['milestone']?></td>
				</tr>
				<tr>
					<th id="h_component">Component:</th>
					<td headers="h_component"><?=$component['name']?></td>
					<th id="h_version">Version:</th>
					<td headers="h_version"><?=$version['version']?></td>
				</tr>
				<tr>
					<th id="h_severity">Severity:</th>
					<td headers="h_severity"><?=ticketseverity($ticket['severity'])?></td>
					<th id="h_status">Status:</th>
					<td headers="h_status"><?=ticketstatus($ticket['status'])?></td>
				</tr>
			</table>
			<div class="description">
				<h3 id="description">Description</h3>
				<p>
					<?=nl2br($ticket['body'])?>
				</p>
			</div>
		</div>
		<div id="history">
			<h3>History</h3>
			<table class="properties">
			<? foreach($history as $info) { ?>
				<tr>
					<th><?=date("g:ia d/m/Y",$info['timestamp'])?>:</th>
					<td>
					<? if($info['type'] == 1) { ?>
					Ticket created by <?=$info['user']['username']?>
					<? } ?>
					</td>
				</tr>
			<? } ?>
			</table>
		</div>
	</div>
<? include(template('footer')); ?>
</body>
</html>