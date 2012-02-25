<div class="group">
	<label><?php echo l('name'); ?></label>
	<?php echo Form::text('name', array('value' => $proj->name)); ?>
</div>
<div class="group">
	<label><?php echo l('slug'); ?></label>
	<?php echo Form::text('slug', array('value' => $proj->slug)); ?> <abbr title="<?php echo l('help.slug'); ?>">?</abbr>
</div>
<div class="group">
	<label><?php echo l('codename'); ?></label>
	<?php echo Form::text('codename', array('value' => $proj->codename)); ?>
</div>
<?php if (!$proj->_is_new()) { ?>
<div class="group">
	<label><?php echo l('private_key'); ?></label>
	<?php echo $proj->private_key; ?>
</div>
<?php } ?>
<div class="group">
	<label><?php echo l('description'); ?></label>
	<?php echo Form::textarea('info', array('value' => $proj->info, 'class' => 'editor')); ?>
</div>