<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=$project['name']?> / <?=l('tickets')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<? require(template('breadcrumbs')); ?>
		
		<h1><?=l('tickets')?></h1>
		<table class="listing tickets">
			<thead>
				<tr>
					<? if(in_array('ticket',$columns)) { ?><th class="id"><?=l('ticket')?></th><? } ?>
					<? if(in_array('summary',$columns)) { ?><th><?=l('summary')?></th><? } ?>
					<? if(in_array('status',$columns)) { ?><th><?=l('status')?></th><? } ?>
					<? if(in_array('owner',$columns)) { ?><th><?=l('owner')?></th><? } ?>
					<? if(in_array('type',$columns)) { ?><th><?=l('type')?></th><? } ?>
					<? if(in_array('severity',$columns)) { ?><th><?=l('severity')?></th><? } ?>
					<? if(in_array('component',$columns)) { ?><th><?=l('component')?></th><? } ?>
					<? if(in_array('milestone',$columns)) { ?><th><?=l('milestone')?></th><? } ?>
					<? if(in_array('version',$columns)) { ?><th><?=l('version')?></th><? } ?>
					<? if(in_array('assigned_to',$columns)) { ?><th><?=l('assigned_to')?></th><? } ?>
				</tr>
			</thead>
			<tbody>
				<? foreach($tickets as $ticket) { ?>
				<tr class="<?=altbg()?> priority<?=$ticket['priority']?>">
					<? if(in_array('ticket',$columns)) { ?><td class="id"><a href="<?=$uri->anchor(PROJECT_SLUG,'ticket-'.$ticket['ticket_id'])?>"><?=$ticket['ticket_id']?></a></td><? } ?>
					<? if(in_array('summary',$columns)) { ?><td><a href="<?=$uri->anchor(PROJECT_SLUG,'ticket-'.$ticket['ticket_id'])?>"><?=$ticket['summary']?></a></td><? } ?>
					<? if(in_array('status',$columns)) { ?><td><?=ticket_status($ticket['status'])?></td><? } ?>
					<? if(in_array('owner',$columns)) { ?><td><?=$ticket['user_name']?></td><? } ?>
					<? if(in_array('type',$columns)) { ?><td><?=ticket_type($ticket['type'])?></td><? } ?>
					<? if(in_array('severity',$columns)) { ?><td><?=$ticket['severity']?></td><? } ?>
					<? if(in_array('component',$columns)) { ?><td><?=$ticket['component']['name']?></td><? } ?>
					<? if(in_array('milestone',$columns)) { ?><td><?=$ticket['milestone']['milestone']?></td><? } ?>
					<? if(in_array('version',$columns)) { ?><td><?=$ticket['version']['version']?></td><? } ?>
					<? if(in_array('assigned_to',$columns)) { ?><td><?=$ticket['assignee']['login']?></td><? } ?>
				</tr>
				<? } ?>
			</tbody>
		</table>
		
		<? require(template('footer')); ?>
	</body>
</html>