<?php if (count($voters)) { ?>
<ol class="voters">
<?php foreach ($voters as $voter) { ?>
	<li><?php echo HTML::link($voter->username, $voter->href()); ?></li>
<?php } ?>
</ol>
<?php } else { ?>
<div>
	<?php echo l('no_votes'); ?>
</div>
<?php } ?>