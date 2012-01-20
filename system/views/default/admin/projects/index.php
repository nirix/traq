<div class="content">
	<h2 id="page_id"><?php echo l('admincp'); ?></h2>
</div>
<?php View::render('admin/_nav'); ?>
<div class="content">
	<?php echo HTML::link(l('new_project'), '/admin/projects/new', array('class' => 'button_new')); ?>
</div>
<div>
	<table class="admin projects list">
		<thead>
			<th class="fixed_name"><?php echo l('name'); ?></th>
			<th class="codename"><?php echo l('codename'); ?></th>
			<th class="actions"><?php echo l('actions'); ?></th>
		</thead>
		<tbody>
		<?php foreach ($projects as $project) { ?>
			<tr>
				<td><?php echo HTML::link($project->name, "/admin/projects/{$project->id}/edit"); ?></td>
				<td><?php echo $project->codename; ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "/admin/projects/{$project->id}/edit", array('title' => l('edit'), 'class' => 'button_edit')); ?>
					<?php echo HTML::link(l('delete'), "/admin/projects/{$project->id}/delete", array('title' => l('delete'), 'class' => 'button_delete', 'data-confirm' => l('confirm:delete_x', $project->name))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>