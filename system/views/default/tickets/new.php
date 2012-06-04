<div class="content">
	<h2 id="page_title"><?php echo l('new_ticket'); ?></h2>

	<div class="tabular box">
		<div class="select group">
			<?php echo Form::label(l('type')); ?>
			<?php echo Form::select('type', TicketType::select_options()); ?>
		</div>
		<div class="group">
			<?php echo Form::label(l('summary'), 'summary'); ?>
			<?php echo Form::text('summary'); ?>
		</div>
		<div class="group">
			<?php echo Form::label(l('description'), 'description'); ?>
			<?php echo Form::textarea('description', array('class' => 'editor')); ?>
		</div>
	</div>
</div>