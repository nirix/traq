<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array(($uri->seg[3] == "open" ? 'Open' : ($uri->seg[3] ? 'Closed' : 'All')).' Tickets '.($uri->seg[2] ? 'for Milestone '.$milestone['milestone'] : ''),$project['name']))?></title>
<? include(template('style')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<h1><?=($uri->seg[3] == "open" ? 'Open' : ($uri->seg[3] ? 'Closed' : 'All'))?> Tickets<? if($uri->seg[2]) {?> for Milestone <?=$milestone['milestone']?><? } ?></h1>

		<table class="listing tickets">
			<thead>
				<tr>
					<th class="id"><a href="?sort=tid&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>">Ticket</a></th>
					<th class="summary"><a href="?sort=summary&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>">Summary</a></th>
					<th class="status"><a href="?sort=status&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>">Status</a></th>
					<th class="owner"><a href="?sort=ownername&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>">Owner</a></th>
					<th class="type"><a href="?sort=type&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>">Type</a></th>
					<th class="priority"><a href="?sort=priority&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>">Priority</a></th>
					<th class="component"><a href="?sort=componentid&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>">Component</a></th>
					<? if(!$uri->seg[2]) { ?>
					<th class="milestone"><a href="?sort=milestoneid&order=<?=($_REQUEST['order'] == 'desc' ? 'asc' : 'desc')?>">Milestone</a></th>
					<? } ?>
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
					<td class="id"><a href="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>"><?=$ticket['tid']?></a></td>
					<td class="summary"><a href="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>"><?=$ticket['summary']?></a></td>
					<td class="status"><?=ticketstatus($ticket['status'])?></td>
					<td class="owner"><?=$ticket['ownername']?></td>
					<td class="type"><?=tickettype($ticket['type'])?></td>
					<td class="priority"><?=ticketpriority($ticket['priority'])?></td>
					<td class="component"><?=$ticket['component']['name']?></td>
					<? if(!$uri->seg[2]) { ?>
					<td class="milestone"><a href="<?=$uri->anchor($project['slug'],'milestone',$ticket['milestone']['milestone'])?>"><?=$ticket['milestone']['milestone']?></a></td>
					<? } ?>
				</tr>
<? } ?>
			</tbody>
		</table>
	</div>
<? include(template('footer')); ?>
</body>
</html>