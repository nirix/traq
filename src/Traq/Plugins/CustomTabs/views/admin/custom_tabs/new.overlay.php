<h3><?= l('new_tab'); ?></h3>
<form action="<?= Request::uri(); ?>" method="post" class="overlay_thin">
	<?php echo show_errors($tab->errors); ?>
	<div class="tabular box">
		<?= view('custom_tabs/_form', [
			'tab' => $tab,
		]); ?>
	</div>
	<div class="actions">
		<input type="submit" value="<?= l('create'); ?>" />
		<input type="button" value="<?= l('cancel'); ?>" onclick="close_overlay();" />
	</div>
</form>
