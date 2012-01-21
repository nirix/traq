<table class="admin projects list">
	<thead>
		<th class="fixed_name"><?php echo l('name'); ?></th>
		<th class="fixed_author"><?php echo l('author'); ?></th>
		<th class="fixed_version"><?php echo l('version'); ?></th>
		<th class="actions"><?php echo l('actions'); ?></th>
	</thead>
	<tbody>
	<?php foreach ($plugins as $plugin) { ?>
		<tr>
			<td><?php echo $plugin['name']; ?></td>
			<td><?php echo $plugin['author'] ? $plugin['author'] : ''; ?></td>
			<td><?php echo $plugin['version']; ?></td>
			<td>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>