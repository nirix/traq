<div class="timeline content">
	<h2 id="page_title"><?php echo l('timeline'); ?></h2>

	<?php foreach ($days as $day) { ?>
	<h3><?php echo Time::date(settings('timeline_day_format'), $day['timestamp']); ?></h3>
	<dl>
	<?php foreach ($day['activity'] as $row) { ?>
		<?php if (in_array($row->action, array('ticket_created','ticket_closed','ticket_reopened'))) { ?>
		<dt>
			<span class="time"><?php echo Time::date(settings('timeline_time_format'), $row->timestamp); ?></span>
			<?php echo HTML::link(
				l("timeline:{$row->action}",
					$row->ticket()->summary,
					$row->ticket()->ticket_id,
					$row->ticket()->type->name,
					$row->ticket_status()->name
				),
				$row->ticket()->href()
			); ?>
		</dt>
		<?php } ?>
		<dd><?php echo l('timeline:by_x', HTML::link($row->user->username, $row->user->href())); ?></dd>
	<?php } ?>
	</dl>
	<?php } ?>
</div>