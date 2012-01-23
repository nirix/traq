<h3><?php echo l('new_milestone'); ?></h3>
<form  action="<?php echo Request::full_uri(); ?>" method="post">
	<?php show_errors($milestone->errors); ?>
	<div class="tabular box">
		<?php View::render('projects/milestones/_form'); ?>
	</div>
	<div class="actions">
		<input type="submit" value="<?php echo l('create'); ?>" />
	</div>
</form>