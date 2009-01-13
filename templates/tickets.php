<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array(($uri->seg[3] == "open" ? 'Open' : 'Closed').' Tickets',$milestone['milestone'],$project['name']))?></title>
<? include(template('style')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<h1><?=($uri->seg[3] == "open" ? 'Open' : 'Closed')?> Tickets for <?=$project['name']?> <?=$milestone['milestone']?></h1>

		<table class="listing tickets">
			<thead>
				<tr>
					<th class="id">Ticket</th>
					<th class="summary">Summary</th>
					<th class="status">Status</th>
					<th class="owner">Owner</th>
					<th class="type">Type</th>
					<th class="priority">Priority</th>
					<th class="component">Component</th>
				</tr>
			</thead>
			<tbody>
<? foreach($tickets as $ticket) {
	if($bgclass == "even") {
		$bgclass = "odd";
	} else {
		$bgclass = "even";
	}
?>
				<tr class="<?=$bgclass?> priority<?=$ticket['priority']?>">
					<td class="id"><a href="<?=$uri->anchor($project['slug'],'ticket',$ticket['id'])?>"><?=$ticket['id']?></a></td>
					<td class="summary"><a href="<?=$uri->anchor($project['slug'],'ticket',$ticket['id'])?>"><?=$ticket['subject']?></a></td>
					<td class="status"><?=ticketstatus($ticket['status'])?></td>
					<td class="owner"><?=$ticket['owner']['username']?></td>
					<td class="type"><?=tickettype($ticket['type'])?></td>
					<td class="priority"><?=ticketpriority($ticket['priority'])?></td>
					<td class="component"><?=$ticket['component']['name']?></td>
				</tr>
<? } ?>
			</tbody>
		</table>
	</div>
<? include(template('footer')); ?>
</body>
</html>