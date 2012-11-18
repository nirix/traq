<div class="new_ticket content">
	<h2 id="page_title"><?php echo l('new_ticket'); ?></h2>

	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php show_errors($ticket->errors); ?>
		<div class="tabular box">
			<div class="group">
				<?php echo Form::label(l('type')); ?>
				<?php echo Form::select('type', Type::select_options()); ?>
			</div>
			<div class="summary group">
				<?php echo Form::label(l('summary'), 'summary'); ?>
				<?php echo Form::text('summary', array('value' => isset(Request::$post['summary']) ? Request::$post['summary'] : '')); ?>
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
					<?php echo Form::select('version', array_merge(array(array('value' => 0, 'label' => l('none'))), $project->milestone_select_options('closed', 'DESC'))); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('component'), 'component'); ?>
					<?php echo Form::select('component', array_merge(array(array('value' => 0, 'label' => l('none'))), Component::select_options($project->id))); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('severity'), 'severity'); ?>
					<?php echo Form::select('severity', Severity::select_options(), array('value' => 4)); ?>
				</div>
				<?php if (current_user()->permission($project->id, 'set_all_ticket_properties')) { ?>
				<div class="field">
					<?php echo Form::label(l('priority'), 'priority'); ?>
					<?php echo Form::select('priority', Priority::select_options(), array('value' => 3)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('status'), 'status'); ?>
					<?php echo Form::select('status', Status::select_options(), array('value' => 1)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('assigned_to'), 'assigned_to'); ?>
					<?php echo Form::select('assigned_to', array_merge(array(array('value' => 0, 'label' => l('none'))), $project->member_select_options()), array('value' => $ticket->assigned_to_id)); ?>
				</div>
				<?php } ?>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="actions">
			<input type="submit" value="<?php echo l('create'); ?>" />
		</div>
	</form>
	<script type="text/javascript">
		$(document).ready(function(){
			if ($('#description').val() == '') {
				traq.load_ticket_template();
			}
			$('#type').change(function() {
				traq.load_ticket_template();
			});
		});
	</script>
</div>