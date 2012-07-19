<div id="ticket_info">
	<h2 id="ticket_summary"><?php echo $ticket->summary; ?></h2>
	<section class="properties">
		<div class="property">
			<?php echo Form::label(l('type')); ?>
			<span class="value"><?php echo $ticket->type->name; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('owner')); ?>
			<span class="value"><?php echo HTML::link($ticket->user->username, $ticket->user->href()); ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('assigned_to')); ?>
			<span class="value"><?php echo $ticket->assigned_to ? HTML::link($ticket->assigned_to->username, $ticket->assigned_to->href()) : ''; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('milestone')); ?>
			<span class="value"><?php echo $ticket->milestone->name; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('version')); ?>
			<span class="value"><?php echo $ticket->version ? $ticket->version->name :''; ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('component')); ?>
			<span class="value"><?php echo $ticket->component ? $ticket->component->name :''; ?></span>
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
				<?php echo HTML::link($ticket->votes, $ticket->href('#'), array('id' => 'votes', 'data-popover' => Request::base($ticket->href('/voters')))); ?>
				<?php if ($current_user->permission($project->id, 'vote_on_tickets') and !in_array($current_user->id, $ticket->extra['voted'])) {
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
				echo HTML::link('', $ticket->href() . '/edit', array('data-overlay' => true, 'class' => 'button_edit'));
			} ?>
		</h3>
		<div class="body">
			<?php echo format_text($ticket->body, true); ?>
		</div>
	</section>
</div>
<div id="ticket_history">
	<h3><?php echo l('ticket_history'); ?></h3>
<?php foreach ($ticket->history->order_by('id', 'DESC')->exec()->fetch_all() as $update) { ?>
	<div class="update">
		<h4><?php echo l('x_by_x', time_ago($update->created_at), HTML::link($update->user->username, $update->user->href())); ?></h4>
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
<?php if ($current_user->permission($project->id, 'update_ticket') or $current_user->permission($project->id, 'comment_on_tickets')) { ?>
<div class="content">
	<h3><?php echo l('update_ticket'); ?></h3>
	<form action="<?php echo Request::full_uri(); ?>/update" method="post" id="update_tickets">
		<div class="tabular box">
			<?php if ($current_user->permission($project->id, 'comment_on_tickets')) { ?>
			<div class="group">
				<?php echo Form::label(l('comment'), 'comment'); ?>
				<?php echo Form::textarea('comment', array('class' => 'editor')); ?>
			</div>
			<?php } ?>
			<?php if ($current_user->permission($project->id, 'update_ticket')) { ?>
			<div class="properties group">
				<div class="field">
					<?php echo Form::label(l('type'), 'type'); ?>
					<?php echo Form::select('type', TicketType::select_options(), array('value' => $ticket->type_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('assigned_to'), 'assigned_to'); ?>
					<?php echo Form::select('assigned_to', array_merge(array(array('value' => '', 'label' => '')), $project->member_select_options()), array('value' => $ticket->assigned_to_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('milestone'), 'milestone'); ?>
					<?php echo Form::select('milestone', $project->milestone_select_options('open')); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('version'), 'version'); ?>
					<?php echo Form::select('version', array_merge(array(array('value' => '0', 'label' => '')), $project->milestone_select_options('closed', 'DESC'))); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('component'), 'component'); ?>
					<?php echo Form::select('component', array_merge(array(array('value' => '0', 'label' => '')), Component::select_options($project->id)), array('value' => $ticket->component_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('severity'), 'severity'); ?>
					<?php echo Form::select('severity', Severity::select_options(), array('value' => $ticket->severity_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('priority'), 'priority'); ?>
					<?php echo Form::select('priority', Priority::select_options(), array('value' => $ticket->priority_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('status'), 'status'); ?>
					<?php echo Form::select('status', TicketStatus::select_options(), array('value' => $ticket->status_id)); ?>
				</div>
				<div class="field">
					<?php echo Form::label(l('summary'), 'summary'); ?>
					<?php echo Form::text('summary', array('value' => htmlspecialchars($ticket->summary))); ?>
				</div>
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