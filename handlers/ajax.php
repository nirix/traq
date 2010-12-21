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


if(@$uri->seg[1] == 'ticket_template')
{
	$type = $db->fetcharray($db->query("SELECT template FROM ".DBPF."ticket_types WHERE id='".$db->es($uri->seg[2])."' LIMIT 1"));
	echo $type['template'];
}
elseif(@$uri->seg[1] == 'ticket_content')
{
	$ticket = $db->fetcharray($db->query("SELECT body FROM ".DBPF."tickets WHERE id='".$db->es($uri->seg[2])."' LIMIT 1"));
	
	if(@$uri->seg[3] == 'save')
	{
		if(empty($_POST['body'])) echo $ticket['body'];
		
		$db->query("UPDATE ".DBPF."tickets SET body='".$db->es($_POST['body'])."' WHERE id='".$db->res($uri->seg[2])."' LIMIT 1");
		echo formattext($_POST['body']);
	} else {
		?><textarea id="new_ticket_content" class="body"><?php echo $ticket['body']; ?></textarea><button type="button" id="update_ticket_save"><?php echo l('update')?></button> <button type="button" id="update_ticket_cancel"><?php echo l('cancel')?></button><?php
	}
}