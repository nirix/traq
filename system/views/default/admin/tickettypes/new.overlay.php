<h3><?php echo l('new_ticket_type'); ?></h3>
<form action="<?php echo Request::full_uri(); ?>" method="post">
	<?php show_errors($type->errors); ?>
	<div class="tabular box">
		<?php View::render('admin/tickettypes/_form'); ?>
	</div>
	<div class="actions">
		<input type="submit" value="<?php echo l('create'); ?>" />
		<input type="button" value="<?php echo l('cancel'); ?>" onclick="close_overlay();" />
	</div>
</form>