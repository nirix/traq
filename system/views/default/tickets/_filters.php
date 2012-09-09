<form action="<?php echo Request::full_uri(); ?>">
	<fieldset>
		<legend><?php echo l('filters'); ?></legend>
		<table>
		<?php foreach ($filters as $filter => $info) { ?>
			<?php foreach ($info['values'] as $k => $value) { ?>
			<tr>
				<td><?php echo $k == 0 ? l($filter) :''; ?></td>
				<td>
					<?php echo $k == 0 ? Form::select("filters[{$filter}][prefix]",
						array(
							array('label' => 'is', 'value' => ''),
							array('label' => 'is not', 'value' => '!')
						),
						array('value' => $info['prefix'])
					) :''; ?>
				</td>
				<td>
					<?php if (in_array($filter, array('milestone', 'status', 'version', 'type'))) {
						echo Form::select("filters[{$filter}][values][]",
							ticket_filter_options_for($filter),
							array('value' => $value)
						);
					} ?>
				</td>
			</tr>
			<?php } ?>
		<?php } ?>
		</table>
	</fieldset>
</form>