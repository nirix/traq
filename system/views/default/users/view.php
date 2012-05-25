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
			<h3><?php echo l('member_of_projects'); ?></h3>
		</div>
	</div>
	
	<div class="span-16 last box">
		<h3><?php echo l('assigned_tickets'); ?></h3>
		<ul>
		<?php foreach ($profile->assigned_tickets()->order_by('updated_at', 'DESC')->exec()->fetch_all() as $ticket) { ?>
			<li><?php echo HTML::link($ticket->summary, $ticket->href()); ?></li>
		<?php } ?>
		</ul>
	</div>
	
	<div class="clearfix"></div>
</div>