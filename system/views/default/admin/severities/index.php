<div class="content">
	<?php echo HTML::link(l('new_severity'), '/admin/severities/new', array('class' => 'button_new', 'data-overlay' => true)); ?>
</div>
<div>
	<table class="list">
		<thead>
			<th class="name"><?php echo l('name'); ?></th>
			<th class="actions"><?php echo l('actions'); ?></th>
		</thead>
		<tbody>
		<?php foreach ($severities as $severity) { ?>
			<tr>
				<td><?php echo HTML::link($severity->name, "/admin/severities/{$severity->id}/edit", array('data-overlay' => true)); ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "/admin/severities/{$severity->id}/edit", array('class' => 'button_edit', 'data-overlay' => true)); ?>
					<?php echo HTML::link(l('delete'), "/admin/severities/{$severity->id}/delete", array('class' => 'button_delete', 'data-confirm' => l('confirm.delete_x', $severity->name))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>