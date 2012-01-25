<div class="content">
	<h3><?php echo l('edit_ticket_type'); ?></h3>
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($type->errors); ?>
		<div class="tabular box">
			<?php View::render('admin/tickettypes/_form'); ?>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('save'); ?>" />
		</div>
	</form>
</div>