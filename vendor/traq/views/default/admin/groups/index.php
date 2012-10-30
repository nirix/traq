<div class="content">
	<?php echo HTML::link(l('new_group'), '/admin/groups/new', array('class' => 'button_new', 'data-overlay' => true)); ?>
</div>
<div>
	<table class="list">
		<thead>
			<th class="fixed_name"><?php echo l('name'); ?></th>
			<th class="user_count"><?php echo l('users'); ?></th>
			<th class="actions"><?php echo l('actions'); ?></th>
		</thead>
		<tbody>
		<?php foreach ($groups as $group) { ?>
			<tr>
				<td><?php echo HTML::link($group->name, "/admin/groups/{$group->id}/edit", array('data-overlay' => true)); ?></td>
				<td><?php echo $group->users->exec()->row_count(); ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "/admin/groups/{$group->id}/edit", array('class' => 'button_edit', 'data-overlay' => true)); ?>
					<?php echo HTML::link(l('delete'), "/admin/groups/{$group->id}/delete", array('class' => 'button_delete', 'data-confirm' => l('confirm.delete_x', $group->name))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>