<?php if (count($component->errors)) { ?>
<div class="error">
	<ul>
	<?php foreach ($component->errors as $error) { ?>
		<li><?php echo $error; ?></li>
	<?php } ?>
	</ul>
</div>
<?php } ?>
<div class="group">
	<label><?php echo l('name'); ?></label>
	<?php echo Form::text('name', array('value' => $component->name)); ?>
</div>