<div class="group">
	<label><?php echo l('name'); ?></label>
	<?php echo Form::text('name', array('value' => $status->name)); ?>
</div>
<div class="group">
	<label><?php echo l('status'); ?></label>
	<?php echo Form::select(
		'status',
		array(
			array('value' => 1, 'label' => l('open')),
			array('value' => 0, 'label' => l('closed'))
		),
		array('value' => $status->status)
	); ?>
</div>
<div class="group">
	<label><?php echo l('show_on_changelog'); ?></label>
	<?php echo Form::checkbox('changelog', 1, array('checked' => $status->changelog)); ?>
</div>