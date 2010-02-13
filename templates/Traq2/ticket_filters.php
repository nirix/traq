<form action="<?=$uri->geturi()?>" method="post">
	<fieldset id="ticket_columns">
		<legend><?=l('columns')?></legend>
		<? foreach(ticket_columns() as $column) { ?>
		<input type="checkbox" name="columns[]" value="<?=$column?>" id="col_<?=$column?>"<?=iif(in_array($column,$columns),' checked="checked"')?> /> <label for="col_<?=$column?>"><?=l($column)?></label>
		<? } ?>
		<input type="submit" value="<?=l('update')?>" />
	</fieldset>
</form>