<form action="<?php echo Request::full_uri(); ?>">
	<fieldset id="ticket_filters">
		<legend><?php echo l('filters'); ?></legend>
		<table>
		<?php foreach ($filters as $filter => $info) { ?>
			<?php foreach ($info['values'] as $k => $value) { ?>
			<tr>
				<td class="label"><?php echo $k == 0 ? l($filter) :''; ?></td>
				<td class="condition">
				<?php
					if ($k == 0) {
						if (in_array($filter, array('summary', 'description'))) {
							echo Form::select("filters[{$filter}][prefix]",
								array(
									array('label' => l('contains'), 'value' => ''),
									array('label' => l('doesnt_contain'), 'value' => '!')
								),
								array('value' => $info['prefix'])
							);
						} else {
							echo Form::select("filters[{$filter}][prefix]",
								array(
									array('label' => l('is'), 'value' => ''),
									array('label' => l('is_not'), 'value' => '!')
								),
								array('value' => $info['prefix'])
							);
						}
					} else {
						echo l('or');
					}
				?>
				</td>
				<td class="value">
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