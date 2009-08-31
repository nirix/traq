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
					<? if(in_array('ticket',$columns)) { ?><th><?=l('ticket')?></th><? } ?>
					<? if(in_array('summary',$columns)) { ?><th><?=l('summary')?></th><? } ?>
				</tr>
			</thead>
			<tbody>
				<? foreach($tickets as $ticket) { ?>
				<tr>
					<? if(in_array('ticket',$columns)) { ?><td><a href="<?=$uri->anchor(PROJECT_SLUG,'ticket-'.$ticket['ticket_id'])?>"><?=$ticket['ticket_id']?></a></td><? } ?>
					<? if(in_array('summary',$columns)) { ?><td><a href="<?=$uri->anchor(PROJECT_SLUG,'ticket-'.$ticket['ticket_id'])?>"><?=$ticket['summary']?></a></td><? } ?>
				</tr>
				<? } ?>
			</tbody>
		</table>
		
		<? require(template('footer')); ?>
	</body>
</html>