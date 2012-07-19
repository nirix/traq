<?php if (count($voters)) { ?>
	<div class="popover-content">
		<ol class="voters">
		<?php foreach (array_slice($voters, 0, 5) as $voter) { ?>
			<li><?php echo HTML::link($voter->username, $voter->href()); ?></li>
		<?php } ?>
		</ol>
	</div>
	<?php if (count($voters) > 5) { ?>
	<div class="popover-footer">
		<?php echo HTML::link(l('see_all'), Request::url() . '?all', array('data-overlay' => true)); ?>
	</div>
	<?php } ?>
<?php } else { ?>
	<div class="popover-content">
		<?php echo l('no_votes'); ?>
	</div>
<?php } ?>