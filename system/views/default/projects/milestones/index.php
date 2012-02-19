<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projects/settings/_nav'); ?>
<div class="content">
	<?php echo HTML::link(l('new_milestone'), "{$project->slug}/settings/milestones/new", array('class' => 'button_new', 'data-overlay' => true)); ?>
</div>
<div>
	<table class="list">
		<thead>
			<tr>
				<th class="fixed_name"><?php echo l('name'); ?></th>
				<th class="codename"><?php echo l('codename'); ?></th>
				<th class="actions"><?php echo l('actions'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($milestones->exec()->fetch_all() as $milestone) { ?>
			<tr>
				<td><?php echo HTML::link($milestone->name, "{$project->slug}/settings/milestones/{$milestone->id}/edit", array('data-overlay' => true)); ?></td>
				<td><?php echo $milestone->codename; ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "{$project->slug}/settings/milestones/{$milestone->id}/edit", array('title' => l('edit'), 'class' => 'button_edit', 'data-overlay' => true)); ?>
					<?php echo HTML::link(l('delete'), "{$project->slug}/settings/milestones/{$milestone->id}/delete", array('title' => l('delete'), 'class' => 'button_delete', 'data-confirm' => l('confirm.delete_x', $milestone->name))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>