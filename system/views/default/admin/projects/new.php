<div class="content">
	<h3><?php echo l('new_project'); ?></h3>
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($proj->errors); ?>
		<div class="tabular box">
			<?php View::render('projectsettings/options/_form'); ?>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('create'); ?>" />
		</div>
	</form>
</div>