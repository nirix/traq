<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projects/settings/_nav'); ?>
<div class="content">
	<h3><?php echo l('edit_milestone'); ?></h3>
	<form action="" method="post" class="tabular box">
		<?php View::render('projects/milestones/_form'); ?>
		<div class="group">
			<input type="submit" value="<?php echo l('save'); ?>" />
		</div>
	</form>
</div>