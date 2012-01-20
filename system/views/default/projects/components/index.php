<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projects/settings/_nav'); ?>
<div class="content">
	<?php echo HTML::link(l('new_component'), "{$project->slug}/settings/components/new", array('class' => 'button_new')); ?>
</div>
<div>
	<table class="list">
		<thead>
			<tr>
				<th class="component_name"><?php echo l('name'); ?></th>
				<th class="actions"><?php echo l('actions'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($components->exec()->fetch_all() as $component) { ?>
			<tr>
				<td><?php echo HTML::link($component->name, "{$project->slug}/settings/components/{$component->id}/edit"); ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "{$project->slug}/settings/components/{$component->id}/edit", array('title' => l('edit'), 'class' => 'button_edit small')); ?>
					<?php echo HTML::link(l('delete'), "{$project->slug}/settings/components/{$component->id}/delete", array('title' => l('delete'), 'class' => 'button_delete small', 'data-confirm' => l('confirm:delete_x', $component->name))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>