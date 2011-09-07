<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo l('tickets')?></title>
		<link rel="alternate" type="application/rss+xml" title="Tickets (RSS 2.0)" href="http://<?php echo $_SERVER['HTTP_HOST'].$uri->anchor($project['slug'],'feeds','tickets')?>" />
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
					<?php if(in_array('ticket',$columns)) { ?><th class="id"><a href="<?php echo ticket_sort_url('id')?>"><?php echo l('ticket')?></a></th><?php } ?>
					<?php if(in_array('summary',$columns)) { ?><th><a href="<?php echo ticket_sort_url('summary')?>"><?php echo l('summary')?></a></th><?php } ?>
					<?php if(in_array('status',$columns)) { ?><th><a href="<?php echo ticket_sort_url('status')?>"><?php echo l('status')?></a></th><?php } ?>
					<?php if(in_array('owner',$columns)) { ?><th><a href="<?php echo ticket_sort_url('user_name')?>"><?php echo l('owner')?></a></th><?php } ?>
					<?php if(in_array('type',$columns)) { ?><th><a href="<?php echo ticket_sort_url('type')?>"><?php echo l('type')?></a></th><?php } ?>
					<?php if(in_array('severity',$columns)) { ?><th><a href="<?php echo ticket_sort_url('severity')?>"><?php echo l('severity')?></a></th><?php } ?>
					<?php if(in_array('component',$columns)) { ?><th><a href="<?php echo ticket_sort_url('component_id')?>"><?php echo l('component')?></a></th><?php } ?>
					<?php if(in_array('milestone',$columns)) { ?><th><a href="<?php echo ticket_sort_url('milestone_id')?>"><?php echo l('milestone')?></a></th><?php } ?>
					<?php if(in_array('version',$columns)) { ?><th><a href="<?php echo ticket_sort_url('version_id')?>"><?php echo l('version')?></a></th><?php } ?>
					<?php if(in_array('assigned_to',$columns)) { ?><th><a href="<?php echo ticket_sort_url('assigned_to')?>"><?php echo l('assigned_to')?></a></th><?php } ?>
					<?php if(in_array('updated',$columns)) { ?><th><a href="<?php echo ticket_sort_url('updated')?>"><?php echo l('updated')?></a></th><?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($tickets as $ticket) { ?>
				<tr class="<?php echo altbg()?> priority<?php echo $ticket['priority']?>">
					<?php if(in_array('ticket',$columns)) { ?><td class="id"><a href="<?php echo $uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'])?>"><?php echo ($ticket['closed'] ? '<s>' : '').$ticket['ticket_id'].($ticket['closed'] ? '</s>' : ''); ?></a></td><?php } ?>
					<?php if(in_array('summary',$columns)) { ?><td><a href="<?php echo $uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'])?>"><?php echo $ticket['summary']?></a></td><?php } ?>
					<?php if(in_array('status',$columns)) { ?><td><?php echo ticket_status($ticket['status'])?></td><?php } ?>
					<?php if(in_array('owner',$columns)) { ?><td><?php echo $ticket['user_name']?></td><?php } ?>
					<?php if(in_array('type',$columns)) { ?><td><?php echo ticket_type($ticket['type'])?></td><?php } ?>
					<?php if(in_array('severity',$columns)) { ?><td><?php echo ticket_severity($ticket['severity'])?></td><?php } ?>
					<?php if(in_array('component',$columns)) { ?><td><?php echo $ticket['component']['name']?></td><?php } ?>
					<?php if(in_array('milestone',$columns)) { ?><td><?php echo $ticket['milestone']['milestone']?></td><?php } ?>
					<?php if(in_array('version',$columns)) { ?><td><?php echo isset($ticket['version']) ? : $ticket['version']['milestone'] : ''; ?></td><?php } ?>
					<?php if(in_array('assigned_to',$columns)) { ?><td><?php echo $ticket['assignee']['username']?></td><?php } ?>
					<?php if(in_array('updated',$columns)) { ?><td><?php echo l('x_ago',timesince(($ticket['updated'] ? $ticket['updated'] : $ticket['created'])))?></td><?php } ?>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		
		<?php require(template('footer')); ?>
	</body>
</html>