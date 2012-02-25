<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projectsettings/_nav'); ?>
<div class="content">
	<h3><?php echo l('edit_component'); ?></h3>
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($component->errors); ?>
		<div class="tabular box">
			<?php View::render('projectsettings/components/_form'); ?>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('save'); ?>" />
		</div>
	</form>
</div>