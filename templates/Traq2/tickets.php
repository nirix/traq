<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo l('tickets')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<h1><?php echo l('tickets')?></h1>
		
		<?php require(template('ticket_filters')) ?>
		
		<table class="listing tickets">
			<thead>
				<tr>
					<?php if(in_array('ticket',$columns)) { ?><th class="id"><?php echo l('ticket')?></th><?php } ?>
					<?php if(in_array('summary',$columns)) { ?><th><?php echo l('summary')?></th><?php } ?>
					<?php if(in_array('status',$columns)) { ?><th><?php echo l('status')?></th><?php } ?>
					<?php if(in_array('owner',$columns)) { ?><th><?php echo l('owner')?></th><?php } ?>
					<?php if(in_array('type',$columns)) { ?><th><?php echo l('type')?></th><?php } ?>
					<?php if(in_array('severity',$columns)) { ?><th><?php echo l('severity')?></th><?php } ?>
					<?php if(in_array('component',$columns)) { ?><th><?php echo l('component')?></th><?php } ?>
					<?php if(in_array('milestone',$columns)) { ?><th><?php echo l('milestone')?></th><?php } ?>
					<?php if(in_array('version',$columns)) { ?><th><?php echo l('version')?></th><?php } ?>
					<?php if(in_array('assigned_to',$columns)) { ?><th><?php echo l('assigned_to')?></th><?php } ?>
					<?php if(in_array('updated',$columns)) { ?><th><?php echo l('updated')?></th><?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($tickets as $ticket) { ?>
				<tr class="<?php echo altbg()?> priority<?php echo $ticket['priority']?>">
					<?php if(in_array('ticket',$columns)) { ?><td class="id"><a href="<?php echo $uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'])?>"><?php echo $ticket['ticket_id']?></a></td><?php } ?>
					<?php if(in_array('summary',$columns)) { ?><td><a href="<?php echo $uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'])?>"><?php echo $ticket['summary']?></a></td><?php } ?>
					<?php if(in_array('status',$columns)) { ?><td><?php echo ticket_status($ticket['status'])?></td><?php } ?>
					<?php if(in_array('owner',$columns)) { ?><td><?php echo $ticket['user_name']?></td><?php } ?>
					<?php if(in_array('type',$columns)) { ?><td><?php echo ticket_type($ticket['type'])?></td><?php } ?>
					<?php if(in_array('severity',$columns)) { ?><td><?php echo ticket_severity($ticket['severity'])?></td><?php } ?>
					<?php if(in_array('component',$columns)) { ?><td><?php echo $ticket['component']['name']?></td><?php } ?>
					<?php if(in_array('milestone',$columns)) { ?><td><?php echo $ticket['milestone']['milestone']?></td><?php } ?>
					<?php if(in_array('version',$columns)) { ?><td><?php echo $ticket['version']['version']?></td><?php } ?>
					<?php if(in_array('assigned_to',$columns)) { ?><td><?php echo $ticket['assignee']['username']?></td><?php } ?>
					<?php if(in_array('updated',$columns)) { ?><td><?php echo l('x_ago',timesince(($ticket['updated'] ? $ticket['updated'] : $ticket['created'])))?></td><?php } ?>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		
		<?php require(template('footer')); ?>
	</body>
</html>