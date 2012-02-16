<?php View::render('wiki/_nav'); ?>
<div class="content">
	<h2 id="page_title"><?php echo $page->title; ?></h2>
	<?php echo format_text($page->body); ?>
</div>