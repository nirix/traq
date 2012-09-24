<div id="ticket_info">
	<h2 id="ticket_summary"><?php echo $ticket->summary; ?></h2>
	<section class="properties">
		<div class="property">
			<?php echo Form::label(l('type')); ?>
			<span class="value"><?php echo $ticket->type->name; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('owner')); ?>
			<span class="value"><?php echo HTML::link(strshorten($ticket->user->name, 20), $ticket->user->href()); ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('assigned_to')); ?>
			<span class="value"><?php echo $ticket->assigned_to ? HTML::link(strshorten($ticket->assigned_to->name, 20), $ticket->assigned_to->href()) : '-'; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('milestone')); ?>
			<span class="value"><?php echo $ticket->milestone ? HTML::link($ticket->milestone->name, $ticket->milestone->href()) : '-'; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('version')); ?>
			<span class="value"><?php echo $ticket->version ? $ticket->version->name : '-'; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('component')); ?>
			<span class="value"><?php echo $ticket->component ? $ticket->component->name : '-'; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('status')); ?>
			<span class="value"><?php echo $ticket->status->name; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('priority')); ?>
			<span class="value"><?php echo $ticket->priority->name; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('severity')); ?>
			<span class="value"><?php echo $ticket->severity->name; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('reported')); ?>
			<span class="value"><?php echo time_ago($ticket->created_at, false, true); ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('updated')); ?>
			<span class="value" id="updated_at"><?php echo $ticket->updated_at > $ticket->created_at ? time_ago($ticket->updated_at, false, true) : l('never'); ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('votes')); ?>
			<span class="value">
				<?php echo ($ticket->votes == 0) ? $ticket->votes : HTML::link($ticket->votes, $ticket->href('#'), array('id' => 'votes', 'data-popover-hover' => Request::base($ticket->href('/voters')))); ?>
				<?php if ($current_user->permission($project->id, 'vote_on_tickets') and !in_array($current_user->id, $ticket->extra['voted']) and $current_user->id != $ticket->user_id) {
					echo HTML::link('+', $ticket->href('/vote'), array('data-ajax' => true));
				} ?>
			</span>
		</div>

		<div class="clearfix"></div>
	</section>
	<section id="description">
		<h3>
			<?php echo l('description'); ?>
			<?php if ($current_user->permission($project->id, 'edit_ticket_description')) {
				echo HTML::link('', $ticket->href() . '/edit', array('title' => l('edit_ticket'), 'data-overlay' => true, 'class' => 'button_edit'));
			} ?>
		</h3>
		<div class="body">
			<?php echo format_text($ticket->body, true); ?>
		</div>
	</section>
	<?php if ($current_user->permission($ticket->project_id, 'view_attachments') and $attachments = $ticket->attachments->exec() and $attachments->row_count() > 0) { ?>
	<section id="attachments">
		<h3><?php echo l('attachments'); ?></h3>
		<ul>
		<?php foreach ($attachments->fetch_all() as $attachment) { ?>
			<li>
				<?php echo l('x_uploaded_by_x_x_ago', HTML::link("<span class=\"attachment_filename\">{$attachment->name}</span>", $attachment->href()), HTML::link(strshorten($attachment->user->name, 20), $attachment->user->href()), time_ago($attachment->created_at, false)); ?>
				<?php if ($current_user->permission($ticket->project_id, 'delete_attachments')) {
					echo HTML::link('', $attachment->href('/delete'), array('class' => 'button_delete', 'data-confirm' => l('confirm.delete_x', $attachment->name)));
				} ?>
			</li>
		<?php } ?>
		</ul>
	</section>
	<?php } ?>
</div>
<div id="ticket_history">
	<h3><?php echo l('ticket_history'); ?></h3>
<?php foreach ($ticket->history->order_by('id', 'DESC')->exec()->fetch_all() as $update) { ?>
	<div class="update" id="ticket_update_<?php echo $update->id; ?>">
		<h4>
			<?php echo l('x_by_x', time_ago($update->created_at), HTML::link(strshorten($update->user->name, 20), $update->user->href())); ?>
			<?php
			if ($current_user->permission($ticket->project_id, 'edit_ticket_history')) {
				echo HTML::link('', $ticket->href("/history/{$update->id}/edit"), array('title' => l('edit'), 'class' => 'button_edit', 'data-overlay' => true));
			}
			if ($current_user->permission($ticket->project_id, 'delete_ticket_history')) {
				echo HTML::link('', $ticket->href("/history/{$update->id}/delete"), array('title' => l('delete'), 'class' => 'button_delete', 'data-ajax-confirm' => l('confirm.delete')));
			}
			?>
		</h4>
		<?php if (is_array($update->changes)) { ?>
		<ul class="changes">
			<?php foreach ($update->changes as $change) { ?>
			<li><?php View::render('tickets/_history_change_bit', array('change' => $change)); ?></li>
			<?php } ?>
		</ul>
		<?php } ?>
		<?php if ($update->comment != '') { ?>
		<div class="comment">
			<?php echo format_text($update->comment); ?>
		</div>
		<?php } ?>
	</div>
<?php } ?>
</div>
<?php if ($current_user->permission($project->id, 'update_tickets') or $current_user->permission($project->id, 'comment_on_tickets')) { ?>
<div class="content">
	<h3><?php echo l('update_ticket'); ?></h3>
	<form action="<?php echo Request::full_uri(); ?>/update" method="post" id="update_tickets" enctype="multipart/form-data">
		<div class="tabular box">
			<?php if ($current_user->permission($project->id, 'comment_on_tickets')) { ?>
			<div class="group">
				<?php echo Form::label(l('comment'), 'comment'); ?>
				<?php echo Form::textarea('comment', array('class' => 'editor')); ?>
			</div>
			<?php } ?>
			<?php if ($current_user->permission($project->id, 'update_tickets')) { ?>
			<div class="properties group">
				<div class="field">
					<?php echo Form::label(l('type'), 'type'); ?>
					<?php echo Form::select('type', Type::select_options(), array('value' => $ticket->type_id)); ?>
				</div>
				<?php if (current_user()->permission($project->id, 'set_all_ticket_properties')) { ?>
				<div class="field">
					<?php echo Form::label(l('assigned_to'), 'assigned_to'); ?>
					<?php echo Form::select('assigned_to', array_merge(array(array('value' => 0, 'label' => l('none'))), $project->member_select_options()), array('value' => $ticket->assigned_to_id)); ?>
				</div>
				<?php } ?>
				<div class="field">
					<?php echo Form::label(l('milestone'), 'milestone'); ?>
					<?php echo Form::select('milestone', $project->milestone_select_options('open'), array('value' => $ticket->milestone_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('version'), 'version'); ?>
					<?php echo Form::select('version', array_merge(array(array('value' => 0, 'label' => l('none'))), $project->milestone_select_options('closed', 'DESC')), array('value' => $ticket->version_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('component'), 'component'); ?>
					<?php echo Form::select('component', array_merge(array(array('value' => 0, 'label' => l('none'))), Component::select_options($project->id)), array('value' => $ticket->component_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('severity'), 'severity'); ?>
					<?php echo Form::select('severity', Severity::select_options(), array('value' => $ticket->severity_id)); ?>
				</div>
				<?php if (current_user()->permission($project->id, 'set_all_ticket_properties')) { ?>
				<div class="field">
					<?php echo Form::label(l('priority'), 'priority'); ?>
					<?php echo Form::select('priority', Priority::select_options(), array('value' => $ticket->priority_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('status'), 'status'); ?>
					<?php echo Form::select('status', Status::select_options(), array('value' => $ticket->status_id)); ?>
				</div>
				<?php } ?>
				<div class="field">
					<?php echo Form::label(l('summary'), 'summary'); ?>
					<?php echo Form::text('summary', array('value' => htmlspecialchars($ticket->summary))); ?>
				</div>
				<?php if ($current_user->permission($project->id, 'add_attachments')) { ?>
				<div class="field">
					<?php echo Form::label(l('attachment'), 'attachment'); ?>
					<input type="file" id="attachment" name="attachment" />
				</div>
				<?php } ?>
			</div>
			<?php } ?>
			<div class="clear"></div>
		</div>
		<div class="actions">
			<?php echo Form::submit(l('submit')); ?>
		</div>
	</form>
</div>
<?php } ?>