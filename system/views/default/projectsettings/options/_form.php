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
<div class="group">
	<label><?php echo l('description'); ?></label>
	<?php echo Form::textarea('info', array('value' => $proj->info, 'class' => 'editor')); ?>
</div>
<div class="group">
	<label><?php echo l('enable_wiki'); ?></label>
	<?php echo Form::radio('enable_wiki', 1, array('checked' => $proj->enable_wiki == 1 ? true : false)); ?> <?php echo Form::label(l('yes'), 'enable_wiki'); ?>
	<?php echo Form::radio('enable_wiki', 0, array('checked' => $proj->enable_wiki == 0 ? true : false, 'id' => 'disable_wiki')); ?> <?php echo Form::label(l('no'), 'disable_wiki'); ?>
</div>