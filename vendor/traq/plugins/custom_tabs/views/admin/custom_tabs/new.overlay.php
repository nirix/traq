<h3><?php echo l('new_tab'); ?></h3>
<form action="<?php echo Request::requestUri(); ?>" method="post" class="overlay_thin">
	<?php echo show_errors($tab->errors); ?>
	<div class="tabular box">
		<?php echo View::render('custom_tabs/_form'); ?>
	</div>
	<div class="actions">
		<input type="submit" value="<?php echo l('create'); ?>" />
		<input type="button" value="<?php echo l('cancel'); ?>" onclick="close_overlay();" />
	</div>
</form>
