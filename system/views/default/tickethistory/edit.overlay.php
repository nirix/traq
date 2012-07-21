<h3><?php echo l('comment'); ?></h3>
<form action="<?php echo Request::full_uri(); ?>" method="post">
	<?php show_errors($history->errors); ?>
	<div class="tabular">
		<?php echo View::render('tickethistory/_form'); ?>
	</div>
	<div class="actions">
		<input type="submit" value="<?php echo l('save'); ?>" />
		<input type="button" value="<?php echo l('cancel'); ?>" onclick="close_overlay();" />
	</div>
</form>