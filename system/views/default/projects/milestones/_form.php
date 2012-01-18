<?php if (count($milestone->errors)) { ?>
<div class="error">
	<ul>
	<?php foreach ($milestone->errors as $error) { ?>
		<li><?php echo $error; ?></li>
	<?php } ?>
	</ul>
</div>
<?php } ?>
<div class="group">
	<label><?php echo l('name'); ?></label>
	<?php echo Form::text('name', array('value' => $milestone->name)); ?>
</div>
<div class="group">
	<label><?php echo l('slug'); ?></label>
	<?php echo Form::text('slug', array('value' => $milestone->slug)); ?> <abbr title="<?php echo l('help:slug'); ?>">?</abbr>
</div>
<div class="group">
	<label><?php echo l('Description'); ?></label>
	<?php echo Form::textarea('info', array('value' => $milestone->info)); ?>
</div>