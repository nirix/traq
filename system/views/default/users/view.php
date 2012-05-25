<div class="profile content">
	<h2><?php echo l('xs_profile', $profile->username); ?></h2>
	
	<div class="sidebar span-6">
		<div class="information box">
			<h3><?php echo l('information'); ?></h3>
			<dl>
				<dt><?php echo l('group'); ?></dt>
				<dd><?php echo $profile->group->name; ?>
				
				<dt><?php echo l('assigned_tickets'); ?></dt>
				<dd><?php echo $profile->assigned_tickets()->exec()->row_count(); ?></dd>
				
				<dt><?php echo l('tickets_created'); ?></dt>
				<dd><?php echo $profile->tickets->exec()->row_count(); ?></dd>
				
				<dt><?php echo l('ticket_updates'); ?></dt>
				<dd><?php echo $profile->ticket_updates->exec()->row_count(); ?></dd>
			</dl>
			
			<div class="clearfix"></div>
		</div>
		
		<div class="box">
			<h3><?php echo l('projects'); ?></h3>
		</div>
	</div>
	
	<div class="span-16 last box">
		<h3><?php echo l('assigned_tickets'); ?></h3>
		
		<table class="list">
			<thead>
				<th><?php echo l('summary'); ?></th>
				<th><?php echo l('owner'); ?></th>
				<th><?php echo l('status'); ?></th>
				<th><?php echo l('created'); ?></th>
				<th><?php echo l('updated'); ?></th>
			</thead>
			<tbody>
			<?php foreach ($profile->assigned_tickets()->order_by('updated_at', 'DESC')->exec()->fetch_all() as $ticket) { ?>
				<tr>
					<td><?php echo HTML::link($ticket->summary, $ticket->href()); ?></td>
					<td><?php echo HTML::link($ticket->user->username, $ticket->user->href()); ?></td>
					<td><?php echo $ticket->status->name; ?></td>
					<td><?php echo l('time.ago', Time::ago_in_words($ticket->created_at)); ?></td>
					<td><?php echo l('time.ago', Time::ago_in_words($ticket->updated_at)); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	
	<div class="clearfix"></div>
</div>