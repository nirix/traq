<div class="milestone content">
	<h2 id="page_title"><?php echo $milestone->name; ?></h2>
	<?php View::render('milestones/_progress'); ?>
	<div class="milestone_info">
		<?php echo format_text($milestone->info); ?>
	</div>
</div>