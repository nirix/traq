<form action="<?=$uri->geturi()?>" method="post">
	<fieldset id="ticket_filters">
		<legend><?=l('filters')?></legend>
		<table width="100%" cellpadding="2" cellspacing="0">
			<? foreach($filters as $filter) {
				$i = -1;
			?>
				<? foreach($filter['values'] as $value) {
					$i++;
				?>
				<tr>
					<td class="label"><?=($i == 0 ? l($filter['type']) :'')?></td>
					<? if($filter['type'] == 'milestone') { ?>
					<td class="mode">
						<? if($i == 0) { ?>
						<select name="modes[<?=$filter['type']?>]">
							<option value=""<?=iif($filter['mode'] == '',' selected="selected"')?>><?=l('is')?></option>
							<option value="!"<?=iif($filter['mode'] == '!',' selected="selected"')?>><?=l('is_not')?></option>
						</select>
						<? } else { ?>
						<?=l('or')?>
						<? } ?>
					</td>
					<? } ?>
					<td class="value"<?=($filter['type'] != 'milestone' ? ' colspan="2"' :'')?>>
						<? if($filter['type'] == 'milestone') { ?>
						<select name="filters[<?=$filter['type']?>][<?=$i?>][value]">
							<? foreach(project_milestones() as $milestone) { ?>
							<option value="<?=$milestone['slug']?>"<?=iif($value == $milestone['slug'],' selected="selected"')?>><?=$milestone['milestone']?></option>
							<? } ?>
						</select>
						<? } elseif($filter['type'] == 'status') { ?>
						
						<? } ?>
					</td>
					<td class="remove">
						<input type="submit" name="rmfilter[<?=$filter['type']?>][<?=$i?>]" value="-" />
					</td>
				</tr>
				<? } ?>
			<? } ?>
			<tr>
				<td><input type="submit" value="<?=l('update')?>" /></td>
				<td align="right" colspan="3">
					<label><?=l('add_filter')?></label>
					<select option="filter">
						<option></option>
					<? foreach(ticket_filters() as $filter) { ?>
						<option value="<?=$filter?>"><?=l($filter)?></option>
					<? } ?>
					</select>
					<input type="submit" value="+" />
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="ticket_columns">
		<legend><?=l('columns')?></legend>
		<? foreach(ticket_columns() as $column) { ?>
		<input type="checkbox" name="columns[]" value="<?=$column?>" id="col_<?=$column?>"<?=iif(in_array($column,$columns),' checked="checked"')?> /> <label for="col_<?=$column?>"><?=l($column)?></label>
		<? } ?>
		<input type="submit" value="<?=l('update')?>" />
	</fieldset>
</form>