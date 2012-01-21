<div class="content">
	<h2 id="page_id"><?php echo l('admincp'); ?></h2>
</div>
<?php View::render('admin/_nav'); ?>
<div class="content">
	<h3 class="list_title"><?php echo l('enabled_plugins'); ?></h3>
</div>
<div>
	<?php View::render('admin/plugins/_list', array('plugins' => $plugins['enabled'])); ?>
</div>
<hr />
<div class="content">
	<h3 class="list_title"><?php echo l('disabled_plugins'); ?></h3>
</div>
<div>
	<?php View::render('admin/plugins/_list', array('plugins' => $plugins['disabled'])); ?>
</div>