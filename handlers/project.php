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

// Get the project info.
$project = $db->queryfirst("SELECT * FROM ".DBPF."projects WHERE slug='".$db->res($uri->seg[0])."' LIMIT 1");
$project['managers'] = explode(',',$project['managers']);

($hook = FishHook::hook('handler_project')) ? eval($hook) : false;

// Project Info
if($uri->seg[1] == '')
{	
	require(template('project_info'));
}
// View Ticket
elseif(preg_match('/ticket-(?<id>\d+)/',$uri->seg[1],$matches))
{
	require(TRAQPATH.'handlers/ticket.php');
}
// View Milestone
elseif(preg_match('/milestone-(?<slug>.*)/',$uri->seg[1],$matches))
{
	require(TRAQPATH.'handlers/milestone.php');
}
// Roadmap
elseif($uri->seg[1] == 'roadmap')
{
	require(TRAQPATH.'handlers/roadmap.php');
}
// View Tickets
elseif($uri->seg[1] == 'tickets')
{
	require(TRAQPATH.'handlers/tickets.php');
}
// New Ticket
elseif($uri->seg[1] == 'newticket')
{
	require(TRAQPATH.'handlers/newticket.php');
}
// Timeline
elseif($uri->seg[1] == 'timeline')
{
	require(TRAQPATH.'handlers/timeline.php');
}
// Changelog
elseif($uri->seg[1] == 'changelog')
{
	require(TRAQPATH.'handlers/changelog.php');
}
// View Attachment
elseif(preg_match('/attachment-(?<id>\d+)/',$uri->seg[1],$matches))
{
	require(TRAQPATH.'handlers/attachment.php');
}
// Source
elseif($uri->seg[1] == 'source')
{
	require(TRAQPATH.'handlers/source.php');
}
// Wiki
elseif($uri->seg[1] == 'wiki')
{
	require(TRAQPATH.'handlers/wiki.php');
}
elseif($uri->seg[1] == 'watch' && $user->loggedin)
{
	if(is_subscribed('project',$project['id']))
		remove_subscription('project',$project['id']);
	else
		add_subscription('project',$project['id']);
	
	header("Location: ".$uri->anchor($project['slug']).'?'.(is_subscribed('project',$project['id']) ? 'subscribed' : 'unsubscribed'));
}
?>