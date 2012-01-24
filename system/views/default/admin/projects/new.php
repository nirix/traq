<div class="content">
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($proj->errors); ?>
		<div class="tabular box">
			<?php View::render('projects/settings/_form'); ?>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('create'); ?>" />
		</div>
	</form>
</div>