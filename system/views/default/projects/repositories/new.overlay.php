<h3><?php echo l('new_repository'); ?></h3>
<form action="<?php echo Request::full_uri(); ?>" method="post" class="overlay_thin">
	<div class="tabular box">
		<?php View::render('projects/repositories/_form'); ?>
	</div>
	<div class="actions">
		<input type="submit" value="<?php echo l('create'); ?>" />
	</div>
</form>