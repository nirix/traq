<?php View::render('wiki/_nav'); ?>
<div class="wiki content">
	<div id="head">
		<h2 id="page_title"><?php echo l('new_page'); ?></h2>
	</div>
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($page->errors); ?>
		<div class="tabular box">
			<?php View::render('wiki/_form', array('page' => $page)); ?>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('create'); ?>" />
		</div>
	</form>
</div>