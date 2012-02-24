<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projects/settings/_nav'); ?>
<div class="content">
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($repo->errors); ?>
		<div class="tabular box">
			<?php View::render('projects/repositories/_form'); ?>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('create'); ?>" />
		</div>
	</form>
</div>