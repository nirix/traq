<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projects/settings/_nav'); ?>
<div class="content">
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($proj->errors); ?>
		<div class="tabular box">
			<?php View::render('projects/settings/_form', array('proj' => $proj)); ?>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('save'); ?>" />
		</div>
	</form>
</div>