<div class="content">
	<div class="page_actions">
		<?php echo HTML::link(l('new_ticket'), $project->href('tickets/new'), array('class' => 'button_bug_new')); ?>
	</div>
	<h2 id="page_title"><?php echo l('tickets')?></h2>
</div>
<table class="list">
	<thead>
	<?php foreach (ticket_columns() as $column) { ?>
		<th><?php echo ticketlist_header($column)?></th>
	<?php } ?>
	</thead>
	<tbody>
	<?php foreach ($tickets as $ticket) { ?>
		<tr>
			<?php foreach (ticket_columns() as $column) { ?>
				<?php if ($column == 'summary') { ?>
				<td><?php echo HTML::link($ticket->summary, "{$project->slug}/tickets/{$ticket->ticket_id}"); ?></td>
				<?php } elseif ($column == 'owner') { ?>
				<td><?php echo HTML::link($ticket->user->username, $ticket->user->href()); ?></td>
				<?php } else { ?>
				<td><?php echo ticketlist_data($column, $ticket); ?></td>
				<?php } ?>
			<?php } ?>
		</tr>
	<?php } ?>
	</tbody>
</table>