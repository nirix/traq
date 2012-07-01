<div class="new_ticket content">
	<h2 id="page_title"><?php echo l('new_ticket'); ?></h2>

	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($ticket->errors); ?>
		<div class="tabular box">
			<div class="group">
				<?php echo Form::label(l('type')); ?>
				<?php echo Form::select('type', TicketType::select_options()); ?>
			</div>
			<div class="summary group">
				<?php echo Form::label(l('summary'), 'summary'); ?>
				<?php echo Form::text('summary'); ?>
			</div>
			<div class="description group">
				<?php echo Form::label(l('description'), 'description'); ?>
				<?php echo Form::textarea('description', array('class' => 'editor')); ?>
			</div>
			<div class="properties group">
				<div class="field">
					<?php echo Form::label(l('milestone'), 'milestone'); ?>
					<?php echo Form::select('milestone', $project->milestone_select_options('open')); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('version'), 'version'); ?>
					<?php echo Form::select('version', array_merge(array(array('value' => '', 'label' => '')), $project->milestone_select_options('closed', 'DESC'))); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('component'), 'component'); ?>
					<?php echo Form::select('component', array_merge(array(array('value' => '', 'label' => '')), Component::select_options($project->id))); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('severity'), 'severity'); ?>
					<?php echo Form::select('severity', Severity::select_options(), array('value' => 4)); ?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('create'); ?>" />
		</div>
	</form>
</div>