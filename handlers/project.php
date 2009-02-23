<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Get the project info
$project = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($uri->seg[0])."' LIMIT 1"));
$project['managerids'] = explode(',',$project['managers']);
$project['desc'] = formattext($project['desc']);
$breadcrumbs[$uri->anchor($project['slug'])] = $project['name'];
FishHook::hook('projecthandler_start');

// Check what page to display
if(!isset($uri->seg[1])) {
	// Project Info page
	FishHook::hook('projecthandler_projectinfo');
	include(template('project'));
} elseif($uri->seg[1] == "roadmap") {
	// Roadmap Page
	$breadcrumbs[$uri->anchor($project['slug'],'roadmap')] = "Roadmap";
	FishHook::hook('projecthandler_roadmap_start');
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project=".$project['id']." AND completed='0' ORDER BY milestone ASC");
	while($info = $db->fetcharray($fetchmilestones)) {
		// Get Ticket Info
		$info['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND milestoneid='".$info['id']."'"));
		$info['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status <= 0 AND milestoneid='".$info['id']."'"));
		$info['tickets']['total'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE milestoneid='".$info['id']."'"));
		$info['tickets']['percent']['closed'] = calculatepercent($info['tickets']['closed'],$info['tickets']['total']);
		$info['tickets']['percent']['open'] = calculatepercent($info['tickets']['open'],$info['tickets']['total']);
		$info['desc'] = formattext($info['desc']);
		FishHook::hook('projecthandler_roadmap_fetchtickets');
		$milestones[] = $info;
	}
	unset($fetchmilestones,$info);
	FishHook::hook('projecthandler_roadmap_pretemplate');
	include(template('roadmap'));
} elseif($uri->seg[1] == "tickets") {
	// Tickets Page
	$breadcrumbs[$uri->anchor($project['slug'],'tickets')] = "Tickets";
	FishHook::hook('projecthandler_tickets_start');
	if($uri->seg[2] && $uri->seg[3]) { // Open or Closed Tickets.
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1"));
		$breadcrumbs[$uri->anchor($project['slug'],'tickets',$milestone['milestone'])] = 'Milestone '.$milestone['milestone'];
		$breadcrumbs[$uri->anchor($project['slug'],'tickets',$milestone['milestone'],$uri->seg[3])] = ($uri->seg[3] == "open" ? 'Open' : 'Closed');
		FishHook::hook('projecthandler_tickets_openorclosed');
		if($uri->seg[3] == "open") {
			$status = "status >= 1";
			$listtype = "open";
		} elseif($uri->seg[3] == "closed") {
			$status = "status <= 0";
			$listtype = "closed";
		}
		// Get Tickets
		$tickets = array();
		$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE $status AND milestoneid='".$milestone['id']."' AND projectid='".$project['id']."' ORDER BY priority DESC");
		while($info = $db->fetcharray($fetchtickets)) {
			$info['summary'] = stripslashes($info['summary']);
			$info['body'] = stripslashes($info['body']);
			$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
			$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
			FishHook::hook('projecthandler_tickets_openorclosed_fetchtickets');
			$tickets[] = $info;
		}
		unset($fetchtickets,$info);
		FishHook::hook('projecthandler_tickets_openorclosed_pretemplate');
		include(template('tickets'));
	} elseif($uri->seg[2] && !$uri->seg[3]) { // Milestone Tickets
		$listtype = "all";
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1"));
		$breadcrumbs[$uri->anchor($project['slug'],'tickets',$milestone['milestone'])] = 'Milestone '.$milestone['milestone'];
		FishHook::hook('projecthandler_tickets_allmilestone');
		// Get Tickets
		$tickets = array();
		$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE milestoneid='".$milestone['id']."' AND projectid='".$project['id']."' ORDER BY priority DESC");
		while($info = $db->fetcharray($fetchtickets)) {
			$info['summary'] = stripslashes($info['summary']);
			$info['body'] = stripslashes($info['body']);
			$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
			$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
			FishHook::hook('projecthandler_tickets_allmilestone_fetchtickets');
			$tickets[] = $info;
		}
		unset($fetchtickets,$info);
		FishHook::hook('projecthandler_tickets_allmilestone_pretemplate');
		include(template('tickets'));
	} else { // All Tickets
		$listtype = "all";
		FishHook::hook('projecthandler_tickets_all');
		// Get Tickets
		$tickets = array();
		$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE projectid='".$project['id']."' ORDER BY priority DESC");
		while($info = $db->fetcharray($fetchtickets)) {
			$info['summary'] = stripslashes($info['summary']);
			$info['body'] = stripslashes($info['body']);
			$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
			$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
			$info['milestone'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$info['milestoneid']."' LIMIT 1"));
			FishHook::hook('projecthandler_tickets_all_fetchtickets');
			$tickets[] = $info;
		}
		unset($fetchtickets,$info);
		FishHook::hook('projecthandler_tickets_all_pretemplate');
		include(template('tickets'));
	}
} else if($uri->seg[1] == "newticket") {
	// Check if user is logged in.
	if(!$user->loggedin) {
		include(template('login'));
		exit;
	}
	$breadcrumbs[$uri->anchor($project['slug'],'newticket')] = "New Ticket";
	FishHook::hook('projecthandler_newticket');
	if($_POST['action'] == "create") {
		$errors = array();
		if($_POST['summary'] == "") {
			$errors['summary'] = "Summary cannot be blank";
		}
		if($_POST['body'] == "") {
			$errors['body'] = "You must enter a description.";
		}
		
		if(!count($errors)) {
			$ticketid = $project['currenttid']+1;
			$db->query("INSERT INTO ".DBPREFIX."tickets VALUES(
															   0,
															   ".$ticketid.",
															   '".$db->escapestring($_POST['summary'])."',
															   '".$db->escapestring($_POST['body'])."',
															   ".$project['id'].",
															   ".$db->escapestring($_POST['milestone']).",
															   ".$db->escapestring($_POST['version']).",
															   ".$db->escapestring($_POST['component']).",
															   ".$db->escapestring($_POST['type']).",
															   1,
															   ".$db->escapestring($_POST['priority']).",
															   ".$db->escapestring($_POST['severity']).",
															   ".$user->info->id.",
															   ".$db->escapestring($_POST['assignto']).",
															   ".time().",
															   0
															   )");
			$internalid = $db->insertid();
			$db->query("UPDATE ".DBPREFIX."projects SET currenttid='".$ticketid."' WHERE id='".$project['id']."' LIMIT 1");
			$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->id.",".$internalid.",'CREATE','')");
			$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,1,'TICKETCREATE:".$internalid."',".time().",NOW(),".$user->info->id.",".$project['id'].")");
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticketid));
		} else {
			FishHook::hook('projecthandler_newticket_pretemplate');
			include(template('newticket'));
		}
	} else {
		FishHook::hook('projecthandler_newticket_pretemplate');
		include(template('newticket'));
	}
} else if($uri->seg[1] == "ticket") {
	// Ticket Page
	$ticket = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE tid='".$db->escapestring($uri->seg[2])."' AND projectid='".$project['id']."' LIMIT 1")); // Get Ticket info
	$ticket['summary'] = stripslashes($ticket['summary']);
	$ticket['body'] = stripslashes($ticket['body']);
	$ticket['body'] = formattext($ticket['body']);
	FishHook::hook('projecthandler_ticketpage_start');
	if($uri->seg[3] == "delete") {
		if($user->group->isadmin or in_array($user->info->id,$project['managerids'])) {
			$db->query("DELETE FROM ".DBPREFIX."tickets WHERE tid='".$ticket['tid']."' AND projectid='".$project['id']."' LIMIT 1");
			$db->query("DELETE FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."' LIMIT 1");
			$db->query("DELETE FROM ".DBPREFIX."attachments WHERE ticketid='".$ticket['id']."' LIMIT 1");
			$db->query("DELETE FROM ".DBPREFIX."timeline WHERE data LIKE 'TICKET%:".$ticket['id']."' LIMIT 1");
			FishHook::hook('projecthandler_deleteticket');
			header("Location: ".$uri->anchor($project['slug'],'tickets'));
		}
	} elseif($_POST['action'] == "deleteattachment") {
		if($user->group->isadmin or in_array($user->info->id,$project['managerids'])) {
			$db->query("DELETE FROM ".DBPREFIX."attachments WHERE id='".$db->escapestring($_POST['attachmentid'])."' LIMIT 1");
			FishHook::hook('projecthandler_deleteattachment');
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid']));
		}
	} elseif($uri->seg[3] == "attachment") {
		$attachment = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."attachments WHERE id='".$db->escapestring($uri->seg[4])."' AND ticketid='".$ticket['id']."' LIMIT 1"));
		header("Content-type: ".$attachment['type']);
		header("Content-Disposition: attachment; filename=\"".$attachment['name']."\"");
		FishHook::hook('projecthandler_viewattachment');
		print(base64_decode($attachment['contents']));
	} else {
		// Update Ticket
		if($_POST['action'] == "update" && $user->loggedin) {
			$changes = array();
			if($user->group->updatetickets) {
				if($_POST['type'] != $ticket['type']) {
					$changes[] = "TYPE:".$_POST['type'].",".$ticket['type'];
				}
				if($_POST['assignto'] != $ticket['assigneeid']) {
					$changes[] = "ASIGNEE:".$_POST['assignto'].",".$ticket['assigneeid'];
				}
				if($_POST['priority'] != $ticket['priority']) {
					$changes[] = "PRIORITY:".$_POST['priority'].",".$ticket['priority'];
				}
				if($_POST['severity'] != $ticket['severity']) {
					$changes[] = "SEVERITY:".$_POST['severity'].",".$ticket['severity'];
				}
				if($_POST['milestone'] != $ticket['milestoneid']) {
					$changes[] = "MILESTONE:".$_POST['milestone'].",".$ticket['milestoneid'];
				}
				if($_POST['version'] != $ticket['versionid']) {
					$changes[] = "VERSION:".$_POST['version'].",".$ticket['versionid'];
				}
				if($_POST['component'] != $ticket['componentid']) {
					$changes[] = "COMPONENT:".$_POST['component'].",".$ticket['componentid'];
				}
				if($_POST['ticketaction'] == "markas") {
					$changes[] = "STATUS:".$_POST['markas'].",".$ticket['status'];
					$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['markas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
				} elseif($_POST['ticketaction'] == "close") {
					$changes[] = "CLOSE:".$_POST['closeas'].",".$ticket['status'];
					$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['closeas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
					$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,2,'TICKETCLOSE:".$ticket['id']."',".time().",NOW(),".$user->info->id.",".$project['id'].")");
				} elseif($_POST['ticketaction'] == "reopen") {
					$changes[] = "REOPEN:".$_POST['reopenas'].",".$ticket['status'];
					$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['reopenas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
					$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,3,'TICKETREOPEN:".$ticket['id']."',".time().",NOW(),".$user->info->id.",".$project['id'].")");
				}
				if(count($changes) > 0) {
					FishHook::hook('projecthandler_updateticket');
					$db->query("UPDATE ".DBPREFIX."tickets SET type='".$db->escapestring($_POST['type'])."',
															   assigneeid='".$db->escapestring($_POST['assignto'])."',
															   priority='".$db->escapestring($_POST['priority'])."',
															   severity='".$db->escapestring($_POST['severity'])."',
															   milestoneid='".$db->escapestring($_POST['milestone'])."',
															   versionid='".$db->escapestring($_POST['version'])."',
															   componentid='".$db->escapestring($_POST['component'])."',
															   updated='".time()."'
															   WHERE id='".$ticket['id']."' LIMIT 1");
				}
			}
			if(!empty($_POST['comment']) or count($changes) > 0) {
				$changes = implode('|',$changes);
				FishHook::hook('projecthandler_updateticket_postcomment');
				$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->id.",".$ticket['id'].",'".$changes."','".$db->escapestring($_POST['comment'])."')");
			}
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid']).'?updated');
		} elseif($uri->seg[3] == "deletecomment") {
			if($user->group->isadmin) {
				$db->query("DELETE FROM ".DBPREFIX."tickethistory WHERE id='".$db->escapestring($uri->seg[4])."' LIMIT 1");
				header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid']));
			}
		}
		// Display Ticket
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$ticket['milestoneid']."' LIMIT 1")); // Get ticket Milestone info
		$version = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$ticket['versionid']."' LIMIT 1")); // Get ticket Version info
		$component = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$ticket['componentid']."' LIMIT 1")); // Get ticket Component info
		$owner = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$ticket['ownerid']."' LIMIT 1")); // Get ticket Owner info
		$assignee = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$ticket['assigneeid']."' LIMIT 1")); // Get ticket Assignee info
		
		$breadcrumbs[$uri->anchor($project['slug'],'tickets')] = "Tickets";
		$breadcrumbs[$uri->anchor($project['slug'],'ticket',$ticket['tid'])] = '#'.$ticket['tid'];
		FishHook::hook('projecthandler_viewticket_start');
		// Ticket History
		$history = array();
		$fetchhistory = $db->query("SELECT * FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."' ORDER BY id ASC");
		while($info = $db->fetcharray($fetchhistory)) {
			$info['user'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$info['userid']."' LIMIT 1"));
			$changes = explode('|',$info['changes']);
			$info['comment_orig'] = $info['comment'];
			$info['comment'] = formattext($info['comment']);
			$info['changes'] = array();
			FishHook::hook('projecthandler_viewticket_fetchhistory');
			foreach($changes as $change) {
				$parts = explode(':',$change);
				$type = $parts[0];
				$values = explode(',',$parts[1]);
				$change = array();
				$change['type'] = $type;
				$change['toid'] = $values[0];
				$change['fromid'] = $values[1];
				if($type == "COMPONENT") {
					$change['from'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$change['fromid']."' LIMIT 1"));
					$change['to'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$change['toid']."' LIMIT 1"));
				} elseif($type == "SEVERITY") {
					$change['from'] = ticketseverity($change['fromid']);
					$change['to'] = ticketseverity($change['toid']);
				} else if($type == "TYPE") {
					$change['from'] = tickettype($change['fromid']);
					$change['to'] = tickettype($change['toid']);
				} else if($type == "ASIGNEE") {
					$change['from'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$change['fromid']."' LIMIT 1"));
					$change['to'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$change['toid']."' LIMIT 1"));
				} else if($type == "MILESTONE") {
					$change['from'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$change['fromid']."' LIMIT 1"));
					$change['to'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$change['toid']."' LIMIT 1"));
				} else if($type == "STATUS") {
					$change['from'] = ticketstatus($change['fromid']);
					$change['to'] = ticketstatus($change['toid']);
				} else if($type == "PRIORITY") {
					$change['from'] = ticketpriority($change['fromid']);
					$change['to'] = ticketpriority($change['toid']);
				} else if($type == "VERSION") {
					$change['from'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$change['fromid']."' LIMIT 1"));
					$change['to'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$change['toid']."' LIMIT 1"));
				} else if($type == "REOPEN") {
					$change['from'] = ticketstatus($change['fromid']);
					$change['to'] = ticketstatus($change['toid']);
				} else if($type == "CLOSE") {
					$change['from'] = ticketstatus($change['fromid']);
					$change['to'] = ticketstatus($change['toid']);
				}
				$info['changes'][] = $change;
			}
			$history[] = $info;
		}
		unset($fetchhistory,$info);
		// Ticket Attachments
		$attachments = array();
		$fetchattachments = $db->query("SELECT * FROM ".DBPREFIX."attachments WHERE ticketid='".$ticket['id']."' ORDER BY timestamp ASC");
		while($info = $db->fetcharray($fetchattachments)) {
			$info['user'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$info['ownerid']."' LIMIT 1"));
			FishHook::hook('projecthandler_viewticket_fetchattachments');
			$attachments[] = $info;
		}
		// Attach File
		if($_POST['action'] == "attachfile") {
			if($user->loggedin) {
				if(!empty($_FILES['file']['name'])) {
					FishHook::hook('projecthandler_ticket_attachfile');
					$db->query("INSERT INTO ".DBPREFIX."attachments VALUES(0,'".$db->escapestring($_FILES['file']['name'])."','".base64_encode(file_get_contents($_FILES['file']['tmp_name']))."','".$_FILES['file']['type']."',".time().",".$user->info->id.",".$ticket['id'].",".$project['id'].")");
				}
			}
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid']));
		}
		FishHook::hook('projecthandler_viewticket_pretemplate');
		include(template('ticket'));
	}
} elseif($uri->seg[1] == "milestone") {
	// Milestone Page
	$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1")); // Get ticket Milestone info
	$milestone['desc'] = formattext($milestone['desc']);
	$milestone['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND milestoneid='".$milestone['id']."'"));
	$milestone['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status <= 0 AND milestoneid='".$milestone['id']."'"));
	$milestone['tickets']['total'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE milestoneid='".$milestone['id']."'"));
	$milestone['tickets']['percent']['closed'] = calculatepercent($milestone['tickets']['closed'],$milestone['tickets']['total']);
	$milestone['tickets']['percent']['open'] = calculatepercent($milestone['tickets']['open'],$milestone['tickets']['total']);
	$breadcrumbs[$uri->anchor($project['slug'],'roadmap')] = "Milestones";
	$breadcrumbs[$uri->anchor($project['slug'],'milestone',$milestone['milestone'])] = $milestone['milestone'];
	FishHook::hook('projecthandler_milestone');
	include(template('milestone'));
} elseif($uri->seg[1] == "timeline") {
	// Timeline Page
	$dates = array();
	$fetchdays = $db->query("SELECT DISTINCT YEAR(date) AS 'year', MONTH(date) AS 'month', DAY(date) AS 'day', date, timestamp FROM ".DBPREFIX."timeline WHERE projectid='".$project['id']."' GROUP BY YEAR(date), MONTH(date), DAY(date) ORDER BY date DESC");
	while($dateinfo = $db->fetcharray($fetchdays)) {
		$row = array();
		$row['date'] = $dateinfo['date'];
		$row['timestamp'] = $dateinfo['timestamp'];
		$row['rows'] = array();
		$fetchrows = $db->query("SELECT * FROM ".DBPREFIX."timeline WHERE projectid='".$project['id']."' AND date='".$dateinfo['date']."' ORDER BY timestamp DESC");
		while($rowinfo = $db->fetcharray($fetchrows)) {
			$parts = explode(':',$rowinfo['data']);
			$rowinfo['type'] = $parts[0];
			$rowinfo['user'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$rowinfo['userid']."' LIMIT 1"));
			if($parts[0] == "TICKETCREATE") {
				$rowinfo['ticket'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE id='".$parts[1]."' LIMIT 1"));
				$rowinfo['ticket']['summary'] = stripslashes($rowinfo['ticket']['summary']);
			} else if($parts[0] == "TICKETCLOSE") {
				$rowinfo['ticket'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE id='".$parts[1]."' LIMIT 1"));
				$rowinfo['ticket']['summary'] = stripslashes($rowinfo['ticket']['summary']);
			} else if($parts[0] == "TICKETREOPEN") {
				$rowinfo['ticket'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE id='".$parts[1]."' LIMIT 1"));
				$rowinfo['ticket']['summary'] = stripslashes($rowinfo['ticket']['summary']);
			}
			FishHook::hook('projecthandler_timeline_fetchrows');
			$row['rows'][] = $rowinfo;
		}
		$dates[] = $row;
	}
	$breadcrumbs[$uri->anchor($projet['slug'],'timeline')] = "Timeline";
	FishHook::hook('projecthandler_timeline_pretemplate');
	include(template('timeline'));
} elseif($uri->seg[1] == "changelog") {
	// Change Log Page
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project='".$project['id']."' ORDER BY milestone DESC");
	while($info = $db->fetcharray($fetchmilestones)) {
		$info['tickets'] = array();
		$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE projectid='".$project['id']."' AND milestoneid='".$info['id']."' AND status <= 0 AND status != -2 ORDER BY updated ASC");
		while($ticketinfo = $db->fetcharray($fetchtickets)) {
			$ticketinfo['summary'] = stripslashes($ticketinfo['summary']);
			$ticketinfo['body'] = stripslashes($ticketinfo['body']);
			$ticketinfo['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$ticketinfo['componentid']."' LIMIT 1"));
			FishHook::hook('projecthandler_changelog_fetchmilestones_fetchchanges');
			$info['tickets'][] = $ticketinfo;
		}
		FishHook::hook('projecthandler_changelog_fetchmilestones');
		$milestones[] = $info;
	}
	$breadcrumbs[$uri->anchor($project['slug'],'changelog')] = "Change Log";
	FishHook::hook('projecthandler_changelog');
	include(template('changelog'));
}
?>