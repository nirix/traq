<div class="content">
	<?php echo HTML::link(l('new_ticket_status'), '/admin/tickets/statuses/new', array('class' => 'button_new')); ?>
</div>
<div>
	<table class="list">
		<thead>
			<th><?php echo l('name'); ?></th>
			<th class="actions"><?php echo l('actions'); ?></th>
		</thead>
		<tbody>
		<?php foreach ($statuses as $status) { ?>
			<tr>
				<td><?php echo HTML::link($status->name, "/admin/tickets/statuses/{$status->id}/edit"); ?></td>
				<td>
					<?php echo HTML::link(l('edit'), "/admin/tickets/statuses/{$status->id}/edit", array('class' => 'button_edit')); ?>
					<?php echo HTML::link(l('delete'), "/admin/tickets/statuses/{$status->id}/delete", array('class' => 'button_delete', 'data-confirm' => l('confirm:delete_x', $status->name))); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>