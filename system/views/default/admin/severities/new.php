<div class="content">
	<h3><?php echo l('new_severity'); ?></h3>
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($severity->errors); ?>
		<div class="tabular box">
			<?php View::render('admin/severities/_form'); ?>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('create'); ?>" />
		</div>
	</form>
</div>