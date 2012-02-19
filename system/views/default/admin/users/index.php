<div class="content">
	<?php echo HTML::link(l('new_user'), '/admin/users/new', array('class' => 'button_new', 'data-overlay' => true)); ?>
</div>
<div>
	<table class="list">
		<thead>
			<th class="fixed_username"><?php echo l('username'); ?></th>
			<th class="usergroup"><?php echo l('group'); ?></th>
			<th class="actions"><?php echo l('actions'); ?></th>
		</thead>
		<tbody>
		<?php foreach ($users as $user) { ?>
			<tr>
				<td><?php echo HTML::link($user->username, "/admin/users/{$user->id}/edit", array('data-overlay' => true)); ?></td>
				<td><?php echo $user->group->name; ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "/admin/users/{$user->id}/edit", array('class' => 'button_edit', 'data-overlay' => true)); ?>
					<?php echo HTML::link(l('delete'), "/admin/users/{$user->id}/delete", array('class' => 'button_delete', 'data-confirm' => l('confirm.delete_x', $user->username))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>