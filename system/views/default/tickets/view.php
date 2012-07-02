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
			<span class="value"><?php echo l('x_ago', Time::ago_in_words($ticket->created_at)); ?></span>
		</div>
		<div class="property">
			<?php echo Form::label(l('updated')); ?>
			<span class="value"><?php echo $ticket->updated_at > $ticket->created_at ? l('x_ago', Time::ago_in_words($ticket->updated_at)) : l('never'); ?></span>
		</div>

		<div class="clearfix"></div>
	</section>
	<section id="description">
		<h3><?php echo l('description'); ?></h3>
		<div class="body">
			<?php echo format_text($ticket->body, true); ?>
		</div>
	</section>
</div>
<div class="content">
	<h3><?php echo l('ticket_history'); ?></h3>
</div>

<?php if ($current_user->permission($project->id, 'update_ticket')) { ?>
<div class="content">
	<h3><?php echo l('update_ticket'); ?></h3>
</div>
<?php } ?>