<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

include("global.php");

authenticate();

// Update
if(isset($_REQUEST['update']))
{
	// Update Types
	if($_POST['action'] == 'type')
	{
		// Loop through the values.
		foreach($_POST['type'] as $id => $values)
		{
			// Make sure the value isnt empty.
			if(!empty($values['name']))
				$db->query("UPDATE ".DBPF."ticket_types SET name='".$db->res($values['name'])."', bullet='".$db->res($values['bullet'])."' WHERE id='".$db->res($id)."' LIMIT 1");
		}
		
		// Check if we're adding one as well...
		if(!empty($_POST['name']))
			$db->query("INSERT INTO ".DBPF."ticket_types (name,bullet) VALUES('".$db->res($_POST['name'])."','".$db->res($_POST['bullet'])."')");
		
		// Go back to the listing
		header("Location: tickets.php?updated");
	}
	// Update Statuses
	elseif($_POST['action'] == 'status')
	{
		// Loop through the values.
		foreach($_POST['statuses'] as $id => $values)
		{
			// Make sure the value isnt empty.
			if(!empty($values['name']))
				$db->query("UPDATE ".DBPF."ticket_status SET name='".$db->res($values['name'])."', status='".$db->res($values['status'])."' WHERE id='".$db->res($id)."' LIMIT 1");
		}
		
		// Check if we're adding one as well...
		if(!empty($_POST['name']))
			$db->query("INSERT INTO ".DBPF."ticket_status (name,status) VALUES('".$db->res($_POST['name'])."','".$db->res($_POST['status'])."')");
		
		// Go back to the listing
		header("Location: tickets.php?updated");
	}
	// Update Priorities
	elseif($_POST['action'] == 'priority')
	{
		// Loop through the values.
		foreach($_POST['priority'] as $id => $value)
		{
			// Make sure the value isnt empty.
			if(!empty($value['name']))
				$db->query("UPDATE ".DBPF."priorities SET name='".$db->res($value)."' WHERE id='".$db->res($id)."' LIMIT 1");
		}
		
		// Go back to the listing
		header("Location: tickets.php?updated");
	}
	// Update Severities
	elseif($_POST['action'] == 'severity')
	{
		// Loop through the values.
		foreach($_POST['severity'] as $id => $value)
		{
			// Make sure the value isnt empty.
			if(!empty($value))
				$db->query("UPDATE ".DBPF."severities SET name='".$db->res($value)."' WHERE id='".$db->res($id)."' LIMIT 1");
		}
		
		// Check if we're adding one as well...
		if(!empty($_POST['name']))
			$db->query("INSERT INTO ".DBPF."severities (name) VALUES('".$db->res($_POST['name'])."')");
		
		// Go back to the listing
		header("Location: tickets.php?updated");
	}
}
// List them all
else
{
	// Get Types
	$types = array();
	$fetchtypes = $db->query("SELECT * FROM ".DBPF."ticket_types ORDER BY name ASC");
	while($type = $db->fetcharray($fetchtypes))
	{
		$types[] = $type;
	}
	
	// Get Statuses
	$statuses = array();
	$fetchstatuses = $db->query("SELECT * FROM ".DBPF."ticket_status ORDER BY name ASC");
	while($status = $db->fetcharray($fetchstatuses))
	{
		$statuses[] = $status;
	}
	
	// Get Priorities
	$priorities = array();
	$fetchpriorities = $db->query("SELECT * FROM ".DBPF."priorities ORDER BY name ASC");
	while($priority = $db->fetcharray($fetchpriorities))
	{
		$priorities[] = $priority;
	}
	
	// Get Severities
	$severities = array();
	$fetchseverities = $db->query("SELECT * FROM ".DBPF."severities ORDER BY name ASC");
	while($severity = $db->fetcharray($fetchseverities))
	{
		$severities[] = $severity;
	}
	
	head(l('tickets'));
	?>
	<h2><?=l('ticket_properties')?></h2>
	
	<form action="tickets.php?update" method="post">
		<input type="hidden" name="action" value="type" />
		<div class="thead"><?=l('type')?></div>
		<div class="tborder">
			<table width="100%" cellspacing="0">
				<tr class="optiontitle first">
					<th width="180" align="left"><?=l('name')?></th>
					<th align="left"><?=l('bullet')?></th>
					<th></th>
				</tr>
				<? foreach($types as $type) { ?>
				<tr>
					<td><input type="text" name="type[<?=$type['id']?>][name]" value="<?=$type['name']?>" /></td>
					<td><input type="text" name="type[<?=$type['id']?>][bullet]" value="<?=$type['bullet']?>" /></td>
					<td align="right">
						<input type="button" value="<?=l('delete')?>" onclick="if(confirm('<?=l('confirm_delete')?>')) { window.location = 'tickets.php?delete&type=<?=$type['id']?>'; }" />
					</td>
				</tr>
				<? } ?>
				<tr>
					<td>
						<input type="text" name="name" value="" /><br />
						<small><?=l('fill_in_to_add_new_type')?></small>
					</td>
					<td colspan="2"><input type="text" name="bullet" value="" /></td>
				</tr>
			</table>
			<div class="tfoot" align="center"><input type="submit" value="<?=l('update')?>" /></div>
		</div>
	</form>
	<br />
	<form action="tickets.php?update" method="post">
		<input type="hidden" name="action" value="status" />
		<div class="thead"><?=l('status')?></div>
		<div class="tborder">
			<table width="100%" cellspacing="0">
				<tr class="optiontitle first">
					<th width="180" align="left"><?=l('name')?></th>
					<th align="left"><?=l('type')?></th>
					<th></th>
				</tr>
				<? foreach($statuses as $status) { ?>
				<tr>
					<td><input type="text" name="statuses[<?=$status['id']?>][name]" value="<?=$status['name']?>" /></td>
					<td>
						<select name="statuses[<?=$status['id']?>][status]">
							<option value="1"<?=iif($status['status'],' selected="selected"')?>><?=l('open')?></option>
							<option value="0"<?=iif(!$status['status'],' selected="selected"')?>><?=l('closed')?></option>
						</select>
					</td>
					<td align="right">
						<input type="button" value="<?=l('delete')?>" onclick="if(confirm('<?=l('confirm_delete')?>')) { window.location = 'tickets.php?delete&status=<?=$status['id']?>'; }" />
					</td>
				</tr>
				<? } ?>
				<tr>
					<td>
						<input type="text" name="name" value="" /><br />
						<small><?=l('fill_in_to_add_new_status')?></small>
					</td>
					<td colspan="2">
						<select name="status">
							<option value="1"><?=l('open')?></option>
							<option value="0"><?=l('closed')?></option>
						</select>
					</td>
				</tr>
			</table>
			<div class="tfoot" align="center"><input type="submit" value="<?=l('update')?>" /></div>
		</div>
	</form>
	<br />
	<form action="tickets.php?update" method="post">
		<input type="hidden" name="action" value="priority" />
		<div class="thead"><?=l('priority')?></div>
		<div class="tborder">
			<table width="100%" cellspacing="0">
				<tr class="optiontitle first">
					<th width="200" align="left"><?=l('name')?></th>
					<th></th>
				</tr>
				<? foreach($priorities as $priority) { ?>
				<tr>
					<td><input type="text" name="priority[<?=$priority['id']?>]" value="<?=$priority['name']?>" /></td>
					<td align="right">
						<input type="button" value="<?=l('delete')?>" onclick="if(confirm('<?=l('confirm_delete')?>')) { window.location = 'tickets.php?delete&priority=<?=$priority['id']?>'; }" />
					</td>
				</tr>
				<? } ?>
			</table>
			<div class="tfoot" align="center"><input type="submit" value="<?=l('update')?>" /></div>
		</div>
	</form>
	<br />
	<form action="tickets.php?update" method="post">
		<input type="hidden" name="action" value="severity" />
		<div class="thead"><?=l('severity')?></div>
		<div class="tborder">
			<table width="100%" cellspacing="0">
				<tr class="optiontitle first">
					<th width="200" align="left"><?=l('name')?></th>
					<th></th>
				</tr>
				<? foreach($severities as $severity) { ?>
				<tr>
					<td><input type="text" name="severity[<?=$severity['id']?>]" value="<?=$severity['name']?>" /></td>
					<td align="right">
						<input type="button" value="<?=l('delete')?>" onclick="if(confirm('<?=l('confirm_delete')?>')) { window.location = 'tickets.php?delete&severity=<?=$severity['id']?>'; }" />
					</td>
				</tr>
				<? } ?>
				<tr>
					<td colspan="2">
						<input type="text" name="name" value="" /><br />
						<small><?=l('fill_in_to_add_new_severity')?></small>
					</td>
				</tr>
			</table>
			<div class="tfoot" align="center"><input type="submit" value="<?=l('update')?>" /></div>
		</div>
	</form>
	<?
	foot();
}
?>