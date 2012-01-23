<h3><?php echo l('edit_milestone'); ?></h3>
<form  action="<?php echo Request::full_uri(); ?>" method="post">
	<div class="tabular">
		<?php View::render('projects/milestones/_form'); ?>
	</div>
	<div class="actions">
		<input type="submit" value="<?php echo l('save'); ?>" />
	</div>
</form>