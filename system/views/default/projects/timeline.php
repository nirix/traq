<div class="timeline content">
	<h2 id="page_title"><?php echo l('timeline'); ?></h2>

	<?php foreach ($days as $day) { ?>
	<h3><?php echo ldate(settings('timeline_day_format'), $day['created_at']); ?></h3>
	<dl>
	<?php foreach ($day['activity'] as $row) { ?>
		<dt>
			<span class="time"><?php echo ldate(settings('timeline_time_format'), $row->created_at); ?></span>
		<?php if (in_array($row->action, array('ticket_created','ticket_closed','ticket_reopened'))) { ?>
			<?php echo HTML::link(
				l("timeline.{$row->action}",
					$row->ticket()->summary,
					$row->ticket()->ticket_id,
					$row->ticket()->type->name,
					$row->ticket_status()->name
				),
				$row->ticket()->href()
			); ?>
		<?php } elseif (in_array($row->action, array('milestone_completed', 'milestone_cancelled'))) { ?>
			<?php echo l("timeline.{$row->action}", $row->milestone()->name); ?>
		<?php } elseif ($row->action == 'ticket_comment') { ?>
			<?php echo l('timeline.ticket_comment', HTML::link($row->ticket()->summary, $row->ticket()->href()), $row->ticket()->ticket_id); ?>
		<?php } ?>
		</dt>
		<dd><?php echo l('timeline.by_x', HTML::link($row->user->name, $row->user->href())); ?></dd>
	<?php } ?>
	</dl>
	<?php } ?>
</div>