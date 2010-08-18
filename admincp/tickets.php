<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
 *
 * This file is part of Traq.
 * 
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
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
				$db->query("UPDATE ".DBPF."ticket_types SET name='".$db->res($values['name'])."', bullet='".$db->res($values['bullet'])."', changelog='".$db->res(($values['changelog'] ? $values['changelog'] : 0))."' WHERE id='".$db->res($id)."' LIMIT 1");
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
				$db->query("UPDATE ".DBPF."ticket_status SET name='".$db->res($values['name'])."', status='".$db->res($values['status'])."', changelog='".$db->res(($values['changelog'] ? $values['changelog'] : 0))."' WHERE id='".$db->res($id)."' LIMIT 1");
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
// Delete
elseif(isset($_REQUEST['delete']))
{
	// Type
	if(isset($_REQUEST['type']))
	{
		// Find a new type
		$new = $db->queryfirst("SELECT id FROM ".DBPF."ticket_types WHERE id!='".$db->res($_REQUEST['type'])."' ORDER BY id ASC LIMIT 1");
		// Update the tickets with the new type id
		$db->query("UPDATE ".DBPF."tickets SET type='".$new['id']."' WHERE type='".$db->res($_REQUEST['type'])."'");
		// Delete the type
		$db->query("DELETE FROM ".DBPF."ticket_types WHERE id='".$db->res($_REQUEST['type'])."' LIMIT 1");
	}
	// Status
	elseif(isset($_REQUEST['status']))
	{
		// Find a new status
		$new = $db->queryfirst("SELECT id FROM ".DBPF."ticket_status WHERE id!='".$db->res($_REQUEST['status'])."' ORDER BY id ASC LIMIT 1");
		// Update the tickets with a new status id
		$db->query("UPDATE ".DBPF."tickets SET status='".$new['id']."' WHERE status='".$db->res($_REQUEST['status'])."'");
		// Delete the status
		$db->query("DELETE FROM ".DBPF."ticket_status WHERE id='".$db->res($_REQUEST['status'])."' LIMIT 1");
	}
	// Priority
	elseif(isset($_REQUEST['priority']))
	{
		// Find a new priority
		$new = $db->queryfirst("SELECT id FROM ".DBPF."priorities WHERE id!='".$db->res($_REQUEST['priority'])."' ORDER BY id ASC LIMIT 1");
		// Update the tickets with a new priority id
		$db->query("UPDATE ".DBPF."tickets SET priority='".$new['id']."' WHERE priority='".$db->res($_REQUEST['priority'])."'");
		// Delete the priority
		$db->query("DELETE FROM ".DBPF."priorities WHERE id='".$db->res($_REQUEST['priority'])."' LIMIT 1");
	}
	// Severity
	elseif(isset($_REQUEST['severity']))
	{
		// Find a new severity
		$new = $db->queryfirst("SELECT id FROM ".DBPF."severities WHERE id!='".$db->res($_REQUEST['severity'])."' ORDER BY id ASC LIMIT 1");
		// Update the tickets with a new severity id
		$db->query("UPDATE ".DBPF."tickets SET severity='".$new['id']."' WHERE severity='".$db->res($_REQUEST['severity'])."'");
		// Delete the severity
		$db->query("DELETE FROM ".DBPF."severities WHERE id='".$db->res($_REQUEST['severity'])."' LIMIT 1");
	}
	header("Location: tickets.php?deleted");
}
// List them all
else
{
	// Get Types
	$types = array();
	$fetchtypes = $db->query("SELECT * FROM ".DBPF."ticket_types ORDER BY id ASC");
	while($type = $db->fetcharray($fetchtypes))
		$types[] = $type;
	
	// Get Statuses
	$statuses = array();
	$fetchstatuses = $db->query("SELECT * FROM ".DBPF."ticket_status ORDER BY id ASC");
	while($status = $db->fetcharray($fetchstatuses))
		$statuses[] = $status;
	
	// Get Priorities
	$priorities = array();
	$fetchpriorities = $db->query("SELECT * FROM ".DBPF."priorities ORDER BY id ASC");
	while($priority = $db->fetcharray($fetchpriorities))
		$priorities[] = $priority;
	
	// Get Severities
	$severities = array();
	$fetchseverities = $db->query("SELECT * FROM ".DBPF."severities ORDER BY id ASC");
	while($severity = $db->fetcharray($fetchseverities))
		$severities[] = $severity;
	
	head(l('tickets'));
	?>
	<h2><?php echo l('ticket_properties')?></h2>
	
	<form action="tickets.php?update" method="post">
		<input type="hidden" name="action" value="type" />
		<div class="thead"><?php echo l('type')?></div>
		<div class="tborder">
			<table width="100%" cellspacing="0">
				<tr class="optiontitle first">
					<th width="180" align="left"><?php echo l('name')?></th>
					<th width="50" align="left"><?php echo l('bullet')?></th>
					<th align="left"><?php echo l('changelog')?></th>
					<th></th>
				</tr>
				<?php foreach($types as $type) { ?>
				<tr>
					<td><input type="text" name="type[<?php echo $type['id']?>][name]" value="<?php echo $type['name']?>" /></td>
					<td><input type="text" name="type[<?php echo $type['id']?>][bullet]" value="<?php echo $type['bullet']?>" style="width:20px;" /></td>
					<td><input type="checkbox" name="type[<?php echo $type['id']?>][changelog]" value="1"<?php echo iif($type['changelog'],' checked="checked"') ?> /></td>
					<td align="right">
						<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$type['name'])?>')) { window.location = 'tickets.php?delete&type=<?php echo $type['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td>
						<input type="text" name="name" value="" /><br />
						<small><?php echo l('fill_in_to_add_new_type')?></small>
					</td>
					<td><input type="text" name="bullet" value="" style="width:20px;" /></td>
					<td colspan="2"><input type="checkbox" name="changelog" value="1" checked="checked" /></td>
				</tr>
			</table>
			<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
		</div>
	</form>
	<br />
	<form action="tickets.php?update" method="post">
		<input type="hidden" name="action" value="status" />
		<div class="thead"><?php echo l('status')?></div>
		<div class="tborder">
			<table width="100%" cellspacing="0">
				<tr class="optiontitle first">
					<th width="180" align="left"><?php echo l('name')?></th>
					<th width="100" align="left"><?php echo l('type')?></th>
					<th width="180" align="left"><?php echo l('changelog')?></th>
					<th></th>
				</tr>
				<?php foreach($statuses as $status) { ?>
				<tr>
					<td><input type="text" name="statuses[<?php echo $status['id']?>][name]" value="<?php echo $status['name']?>" /></td>
					<td>
						<select name="statuses[<?php echo $status['id']?>][status]">
							<option value="1"<?php echo iif($status['status'],' selected="selected"')?>><?php echo l('open')?></option>
							<option value="0"<?php echo iif(!$status['status'],' selected="selected"')?>><?php echo l('closed')?></option>
						</select>
					</td>
					<td><input type="checkbox" name="statuses[<?php echo $status['id']?>][changelog]" value="1"<?php echo iif($status['changelog'],' checked="checked"') ?> /></td>
					<td align="right">
						<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$status['name'])?>')) { window.location = 'tickets.php?delete&status=<?php echo $status['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td>
						<input type="text" name="name" value="" /><br />
						<small><?php echo l('fill_in_to_add_new_status')?></small>
					</td>
					<td>
						<select name="status">
							<option value="1"><?php echo l('open')?></option>
							<option value="0"><?php echo l('closed')?></option>
						</select>
					</td>
					<td colspan="2"><input type="checkbox" name="changelog" value="1" checked="checked" /></td>
				</tr>
			</table>
			<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
		</div>
	</form>
	<br />
	<form action="tickets.php?update" method="post">
		<input type="hidden" name="action" value="priority" />
		<div class="thead"><?php echo l('priority')?></div>
		<div class="tborder">
			<table width="100%" cellspacing="0">
				<tr class="optiontitle first">
					<th width="200" align="left"><?php echo l('name')?></th>
					<th></th>
				</tr>
				<?php foreach($priorities as $priority) { ?>
				<tr>
					<td><input type="text" name="priority[<?php echo $priority['id']?>]" value="<?php echo $priority['name']?>" /></td>
					<td align="right">
						<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$priority['name'])?>')) { window.location = 'tickets.php?delete&priority=<?php echo $priority['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
					</td>
				</tr>
				<?php } ?>
			</table>
			<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
		</div>
	</form>
	<br />
	<form action="tickets.php?update" method="post">
		<input type="hidden" name="action" value="severity" />
		<div class="thead"><?php echo l('severity')?></div>
		<div class="tborder">
			<table width="100%" cellspacing="0">
				<tr class="optiontitle first">
					<th width="200" align="left"><?php echo l('name')?></th>
					<th></th>
				</tr>
				<?php foreach($severities as $severity) { ?>
				<tr>
					<td><input type="text" name="severity[<?php echo $severity['id']?>]" value="<?php echo $severity['name']?>" /></td>
					<td align="right">
						<a href="#" onclick="if(confirm('<?php echo l('confirm_delete_x',$severity['name'])?>')) { window.location = 'tickets.php?delete&severity=<?php echo $severity['id']?>'; } return false;"><img src="images/delete.png" alt="<?php echo l('delete')?>" title="<?php echo l('delete')?>" /></a>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="2">
						<input type="text" name="name" value="" /><br />
						<small><?php echo l('fill_in_to_add_new_severity')?></small>
					</td>
				</tr>
			</table>
			<div class="tfoot" align="center"><input type="submit" value="<?php echo l('update')?>" /></div>
		</div>
	</form>
	<?php
	foot();
}
?>