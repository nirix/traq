<div class="content">
	<h2 id="page_title"><?php echo l('project_settings'); ?></h2>
</div>
<?php View::render('projectsettings/_nav'); ?>

<form action="<?php echo Request::full_uri(); ?>" method="post">
	<table class="list">
		<thead>
			<tr>
				<th><?php echo l('action'); ?></th>
			<?php foreach ($groups as $group) { ?>
				<th><?php echo $group->name; ?></th>
			<?php } ?>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($actions as $action) { ?>
			<tr>
				<td><strong><?php echo l("permissions.{$action}"); ?></strong></td>
			<?php foreach ($groups as $group) {
				$perm = $permissions[$group->id][$action];
				?>
				<td>
					<?php
						echo Form::select("perm[{$group->id}][{$perm->id}]",
							($group->id == 0 ? $options['defaults'] : $options['all']),
							array(
								'value' => ($group->id == 0 ? $perm->value : ($perm->type_id == 0 ? -1 : $perm->value))
							)
						);
					?>
				</td>
			<?php } ?>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<div class="actions">
		<?php echo Form::submit(l('save')); ?>
	</div>
</form>