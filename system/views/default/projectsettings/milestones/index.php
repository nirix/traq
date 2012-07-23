<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projectsettings/_nav'); ?>
<div class="content">
	<?php echo HTML::link(l('new_milestone'), "{$project->slug}/settings/milestones/new", array('class' => 'button_new', 'data-overlay' => true)); ?>
</div>
<div>
	<table class="list">
		<thead>
			<tr>
				<th class="fixed_name"><?php echo l('name'); ?></th>
				<th class="codename"><?php echo l('codename'); ?></th>
				<th class="due"><?php echo l('due'); ?></th>
				<th class="status"><?php echo l('status'); ?></th>
				<th class="actions"><?php echo l('actions'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($milestones->exec()->fetch_all() as $milestone) { ?>
			<tr>
				<td><?php echo HTML::link($milestone->name, "{$project->slug}/settings/milestones/{$milestone->id}/edit", array('data-overlay' => true)); ?></td>
				<td><?php echo $milestone->codename; ?></td>
				<td><?php echo $milestone->due > 0 ? time_from_now($milestone->due, true) : ''; ?></td>
				<td><?php echo $milestone->status == 0 ? l('cancelled') : ($milestone->status == 1 ? l('active') : l('completed')); ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "{$project->slug}/settings/milestones/{$milestone->id}/edit", array('class' => 'button_edit', 'data-overlay' => true)); ?>
					<?php echo HTML::link(l('delete'), "{$project->slug}/settings/milestones/{$milestone->id}/delete", array('class' => 'button_delete', 'data-confirm' => l('confirm.delete_x', $milestone->name))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>