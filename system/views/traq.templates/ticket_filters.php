<form action="<?php echo $uri->geturi()?>" method="post">
	<fieldset id="ticket_filters">
		<legend><?php echo l('filters')?></legend>
		<table width="100%" cellpadding="2" cellspacing="0">
			<?php foreach($filters as $filter) {
				$val = -1;
			?>
			<?php if(in_array($filter['type'],array('milestone','version','type','component','severity','priority','owner'))) { ?>
			<?php foreach($filter['values'] as $value) { $val++; ?>
			<tr>
				<td class="label"><?php echo iif($val == 0,l($filter['type']))?></td>
				<td class="mode">
					<?php if($val == 0) { ?>
					<select name="modes[<?php echo $filter['type']?>]">
						<option value=""<?php echo iif(isset($filter['mode']) && $filter['mode'] == '',' selected="selected"')?>><?php echo l('is')?></option>
						<option value="!"<?php echo iif(isset($filter['mode']) && $filter['mode'] == '!',' selected="selected"')?>><?php echo l('is_not')?></option>
					</select>
					<?php } else { ?>
					<?php echo l('or')?>
					<?php } ?>
				</td>
				<td class="value">
					<?php if($filter['type'] == 'milestone') { ?>
					<select name="filters[<?php echo $filter['type']?>][<?php echo $val?>][value]">
						<option></option>
						<?php foreach(project_milestones() as $milestone) { ?>
						<option value="<?php echo $milestone['slug']?>"<?php echo iif($value == $milestone['slug'],' selected="selected"')?>><?php echo $milestone['milestone']?></option>
						<?php } ?>
					</select>
					<?php } elseif($filter['type'] == 'version') { ?>
					<select name="filters[<?php echo $filter['type']?>][<?php echo $val?>][value]">
						<option></option>
						<?php foreach(project_milestones() as $version) { ?>
						<option value="<?php echo $version['id']?>"<?php echo iif($value == $version['id'],' selected="selected"')?>><?php echo $version['milestone']?></option>
						<?php } ?>
					</select>
					<?php } elseif($filter['type'] == 'type') { ?>
					<select name="filters[<?php echo $filter['type']?>][<?php echo $val?>][value]">
						<option></option>
						<?php foreach(ticket_types() as $type) { ?>
						<option value="<?php echo $type['id']?>"<?php echo iif($value == $type['id'],' selected="selected"')?>><?php echo $type['name']?></option>
						<?php } ?>
					</select>
					<?php } elseif($filter['type'] == 'component') { ?>
					<select name="filters[<?php echo $filter['type']?>][<?php echo $val?>][value]">
						<option></option>
						<?php foreach(project_components() as $component) { ?>
						<option value="<?php echo $component['id']?>"<?php echo iif($value == $component['id'],' selected="selected"')?>><?php echo $component['name']?></option>
						<?php } ?>
					</select>
					<?php } elseif($filter['type'] == 'severity') { ?>
					<select name="filters[<?php echo $filter['type']?>][<?php echo $val?>][value]">
						<option></option>
						<?php foreach(ticket_severities() as $severity) { ?>
						<option value="<?php echo $severity['id']?>"<?php echo iif($value == $severity['id'],' selected="selected"')?>><?php echo $severity['name']?></option>
						<?php } ?>
					</select>
					<?php } elseif($filter['type'] == 'priority') { ?>
					<select name="filters[<?php echo $filter['type']?>][<?php echo $val?>][value]">
						<option></option>
						<?php foreach(ticket_priorities() as $priority) { ?>
						<option value="<?php echo $priority['id']?>"<?php echo iif($value == $priority['id'],' selected="selected"')?>><?php echo $priority['name']?></option>
						<?php } ?>
					</select>
					<?php } elseif($filter['type'] == 'owner') { ?>
					<input type="text" name="filters[<?php echo $filter['type']?>][<?php echo $val?>][value]" value="<?php echo $value?>" />
					<?php } ?>
				</td>
				<td class="remove">
					<input type="submit" name="rmfilter[<?php echo $filter['type']?>][<?php echo $val?>]" value="-" />
				</td>
			</tr>
			<?php } ?>
			<?php } elseif($filter['type'] == 'summary' or $filter['type'] == 'description') {?>
			<?php foreach($filter['values'] as $value) { $val++; ?>
			<tr>
				<td class="label"><?php echo iif($val == 0,l($filter['type']))?></td>
				<td class="mode">
					<?php if($val == 0) { ?>
					<select name="modes[<?php echo $filter['type']?>]">
						<option value=""<?php echo iif(isset($filter['mode']) && $filter['mode'] == '',' selected="selected"')?>><?php echo l('contains')?></option>
						<option value="!"<?php echo iif(isset($filter['mode']) && $filter['mode'] == '!',' selected="selected"')?>><?php echo l('does_not_contain')?></option>
					</select>
					<?php } else { ?>
					<?php echo l('or')?>
					<?php } ?>
				</td>
				<td class="value">
					<input type="text" name="filters[<?php echo $filter['type']?>][<?php echo $val?>][value]" value="<?php echo $value?>" />
				</td>
				<td class="remove">
					<input type="submit" name="rmfilter[<?php echo $filter['type']?>]" value="-" />
				</td>
			</tr>
			<?php } ?>
			<?php } elseif($filter['type'] == 'status') { ?>
			<tr>
				<td class="label"><?php echo l($filter['type'])?></td>
				<td class="value" colspan="2">
					<?php foreach(ticket_status_list('all') as $status) { ?>
						<input type="checkbox" name="filters[status][]" value="<?php echo $status['id']?>" id="filter_status_<?php echo $status['id']?>"<?php echo iif(in_array($status['id'],$filter['values']) or ($filter['value'] == 'open' && $status['status'] == 1) or ($filter['value'] == 'closed' && $status['status'] == 0),' checked="checked"')?> /> <label for="filter_status_<?php echo $status['id']?>"><?php echo $status['name']?></label>
					<?php } ?>
				</td>
				<td class="remove">
					<input type="submit" name="rmfilter[<?php echo $filter['type']?>]" value="-" />
				</td>
			</tr>
			<?php } ?>
			<?php } ?>
			<tr>
				<td><input type="submit" value="<?php echo l('update')?>" /></td>
				<td class="add_filter" align="right" colspan="3">
					<label><small><?php echo l('add_filter')?></small></label>
					<select name="add_filter">
						<option></option>
					<?php foreach(ticket_filters() as $filter) { ?>
						<option value="<?php echo $filter?>"><?php echo l($filter)?></option>
					<?php } ?>
					</select>
					<input type="submit" value="+" />
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="ticket_columns">
		<legend><?php echo l('columns')?></legend>
		<?php foreach(ticket_columns() as $column) { ?>
		<input type="checkbox" name="columns[]" value="<?php echo $column?>" id="col_<?php echo $column?>"<?php echo iif(in_array($column,$columns),' checked="checked"')?> /> <label for="col_<?php echo $column?>"><?php echo l($column)?></label>
		<?php } ?>
		<div>
			<input type="submit" value="<?php echo l('update')?>" />
		</div>
	</fieldset>
</form>