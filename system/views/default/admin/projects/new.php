<div class="content">
	<h2 id="page_title"><?php echo l('admincp'); ?></h2>
</div>
<?php View::render('admin/_nav'); ?>
<div class="content">
	<form action="<?php echo Request::full_uri(); ?>" method="post" class="tabular box">
		<?php View::render('projects/settings/_form'); ?>
		<div class="group">
			<input type="submit" value="<?php echo l('create'); ?>" />
		</div>
	</form>
</div>