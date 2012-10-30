<div class="content">
	<?php echo HTML::link(l('new_priority'), '/admin/priorities/new', array('class' => 'button_new', 'data-overlay' => true)); ?>
</div>
<div>
	<table class="list">
		<thead>
			<th class="name"><?php echo l('name'); ?></th>
			<th class="actions"><?php echo l('actions'); ?></th>
		</thead>
		<tbody>
		<?php foreach ($priorities as $priority) { ?>
			<tr>
				<td><?php echo HTML::link($priority->name, "/admin/priorities/{$priority->id}/edit", array('data-overlay' => true)); ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "/admin/priorities/{$priority->id}/edit", array('class' => 'button_edit', 'data-overlay' => true)); ?>
					<?php echo HTML::link(l('delete'), "/admin/priorities/{$priority->id}/delete", array('class' => 'button_delete', 'data-confirm' => l('confirm.delete_x', $priority->name))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>