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
 */

include("global.php");

authenticate();

// Edit
if(isset($_REQUEST['edit']))
{
	$type = $db->fetcharray($db->query("SELECT id, name, template FROM ".DBPF."ticket_types WHERE id='".$db->es($_REQUEST['edit'])."' LIMIT 1"));
	
	if(isset($_POST['action']) && $_POST['action'] == 'save')
	{
		$db->query("UPDATE ".DBPF."ticket_types SET template='".$db->es($_POST['template'])."' WHERE id='".$type['id']."' LIMIT 1");
		header("Location: tickets.php");
	}
	
	head(l('edit_ticket_template'));
	?>
	<form action="ticket_templates.php?edit=<?php echo (int)$_REQUEST['edit']?>" method="post">
	<input type="hidden" name="action" value="save" />
	<div class="thead"><?php echo l('edit_ticket_template_x',$type['name'])?></div>
	<div class="tborder">
		<table width="100%" cellspacing="0">
			<tr>
				<td class="optiontitle first" colspan="2"><?php echo l('type')?></td>
			</tr>
			<tr class="<?php echo altbg()?>">
				<td></td>
				<td width="200"><input type="text" name="name" disabled="disabled" value="<?php echo $type['name']?>" /></td>
			</tr>
			<tr>
				<td class="optiontitle" colspan="2"><?php echo l('template')?></td>
			</tr>
				<tr class="<?php echo altbg()?>">
					<td colspan="2"><textarea name="template" style="width:100%;height:200px"><?php echo $type['template']?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" class="tfoot"><div align="center"><input type="submit" value="<?php echo l('update')?>" /></div></td>
				</tr>
		</table>
	</div>
	</form>
	<?php
	foot();
}