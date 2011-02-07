<?php
/**
 * Traq 2
 * Copyright (C) 2009-2011 Jack Polgar
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
if($uri->seg(1) == '')
{	
	$tickets = array(
		'open' => $db->numrows($db->query("SELECT id FROM ".DBPF."tickets WHERE project_id='".$project['id']."' AND closed=0")),
		'closed' => $db->numrows($db->query("SELECT id FROM ".DBPF."tickets WHERE project_id='".$project['id']."' AND closed=1"))
	);
	$milestones = array(
		'open' => $db->numrows($db->query("SELECT id FROM ".DBPF."milestones WHERE project_id=".$project['id']." AND completed=0")),
		'completed' => $db->numrows($db->query("SELECT id FROM ".DBPF."milestones WHERE project_id=".$project['id']." AND completed > 1"))
	);
	require(template('project_info'));
}
// View Ticket
elseif(preg_match('/ticket-(?P<id>\d+)/',$uri->seg(1),$matches))
{
	require(TRAQPATH.'system/controllers/ticket.php');
}
// View Milestone
elseif(preg_match('/milestone-(?P<slug>.*)/',$uri->seg(1),$matches))
{
	require(TRAQPATH.'system/controllers/milestone.php');
}
// Roadmap
elseif($uri->seg(1) == 'roadmap')
{
	require(TRAQPATH.'system/controllers/roadmap.php');
}
// View Tickets
elseif($uri->seg(1) == 'tickets')
{
	require(TRAQPATH.'system/controllers/tickets.php');
}
// New Ticket
elseif($uri->seg(1) == 'newticket')
{
	require(TRAQPATH.'system/controllers/newticket.php');
}
// Timeline
elseif($uri->seg(1) == 'timeline')
{
	require(TRAQPATH.'system/controllers/timeline.php');
}
// Changelog
elseif($uri->seg(1) == 'changelog')
{
	require(TRAQPATH.'system/controllers/changelog.php');
}
// View Attachment
elseif(preg_match('/attachment-(?P<id>\d+)/',$uri->seg(1),$matches))
{
	require(TRAQPATH.'system/controllers/attachment.php');
}
// Source
elseif($uri->seg(1) == 'source')
{
	require(TRAQPATH.'system/controllers/source.php');
}
// Wiki
elseif($uri->seg(1) == 'wiki')
{
	require(TRAQPATH.'system/controllers/wiki.php');
}
// Feeds
elseif($uri->seg(1) == 'feeds')
{
	require(TRAQPATH.'system/controllers/feeds.php');
}
// Watch
elseif($uri->seg(1) == 'watch' && $user->loggedin)
{
	if(is_subscribed('project',$project['id']))
		remove_subscription('project',$project['id']);
	else
		add_subscription('project',$project['id']);
	
	header("Location: ".$uri->anchor($project['slug']).'?'.(is_subscribed('project',$project['id']) ? 'subscribed' : 'unsubscribed'));
}