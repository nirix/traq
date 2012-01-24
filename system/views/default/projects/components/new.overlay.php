<h3><?php echo l('new_component'); ?></h3>
<form action="<?php echo Request::full_uri(); ?>" method="post" class="overlay_thin">
	<?php show_errors($component->errors); ?>
	<div class="tabular">
		<?php View::render('projects/components/_form'); ?>
	</div>
	<div class="actions">
		<input type="submit" value="<?php echo l('create'); ?>" />
	</div>
</form>