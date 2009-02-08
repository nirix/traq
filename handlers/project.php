<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Get the project info
$project = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($uri->seg[0])."' LIMIT 1"));
$project['managerids'] = explode(',',$project['managers']);
$breadcrumbs[$uri->anchor($project['slug'])] = $project['name'];

// Check what page to display
if(!isset($uri->seg[1])) {
	// Project Info page
	include(template('project'));
} elseif($uri->seg[1] == "roadmap") {
	// Roadmap Page
	$breadcrumbs[$uri->anchor($project['slug'],'roadmap')] = "Roadmap";
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project=".$project['id']." ORDER BY milestone ASC");
	while($info = $db->fetcharray($fetchmilestones)) {
		// Get Ticket Info
		$info['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND milestoneid='".$info['id']."'"));
		$info['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status <= 0 AND milestoneid='".$info['id']."'"));
		$info['tickets']['total'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE milestoneid='".$info['id']."'"));
		$info['tickets']['percent']['closed'] = calculatepercent($info['tickets']['closed'],$info['tickets']['total']);
		$info['tickets']['percent']['open'] = calculatepercent($info['tickets']['open'],$info['tickets']['total']);
		$milestones[] = $info;
	}
	unset($fetchmilestones,$info);
	include(template('roadmap'));
} elseif($uri->seg[1] == "tickets") {
	// Tickets Page
	$breadcrumbs[$uri->anchor($project['slug'],'tickets')] = "Tickets";
	if($uri->seg[2] && $uri->seg[3]) { // Open or Closed Tickets.
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1"));
		$breadcrumbs[$uri->anchor($project['slug'],'tickets',$milestone['milestone'])] = 'Milestone '.$milestone['milestone'];
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
			$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
			$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
			$tickets[] = $info;
		}
		unset($fetchtickets,$info);
		include(template('tickets'));
	} elseif($uri->seg[2] && !$uri->seg[3]) { // Milestone Tickets
		$listtype = "all";
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1"));
		$breadcrumbs[$uri->anchor($project['slug'],'tickets',$milestone['milestone'])] = $milestone['milestone'];
		// Get Tickets
		$tickets = array();
		$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE milestoneid='".$milestone['id']."' AND projectid='".$project['id']."' ORDER BY priority DESC");
		while($info = $db->fetcharray($fetchtickets)) {
			$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
			$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
			$tickets[] = $info;
		}
		unset($fetchtickets,$info);
		include(template('tickets'));
	} else { // All Tickets
		$listtype = "all";
		// Get Tickets
		$tickets = array();
		$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE projectid='".$project['id']."' ORDER BY priority DESC");
		while($info = $db->fetcharray($fetchtickets)) {
			$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
			$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
			$info['milestone'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$info['milestoneid']."' LIMIT 1"));
			$tickets[] = $info;
		}
		unset($fetchtickets,$info);
		include(template('tickets'));
	}
} else if($uri->seg[1] == "newticket") {
	// Check if user is logged in.
	if(!$user->loggedin) {
		include(template('login'));
		exit;
	}
	$breadcrumbs[$uri->anchor($project['slug'],'newticket')] = "New Ticket";
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
															   ".$user->info->uid.",
															   ".$db->escapestring($_POST['assignto']).",
															   ".time().",
															   0
															   )");
			$internalid = $db->insertid();
			$db->query("UPDATE ".DBPREFIX."projects SET currenttid='".$ticketid."' WHERE id='".$project['id']."' LIMIT 1");
			$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->uid.",".$internalid.",'CREATE')");
			$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,1,'TICKETCREATE:".$internalid."',".time().",NOW(),".$project['id'].")");
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticketid));
		} else {
			include(template('newticket'));
		}
	} else {
		include(template('newticket'));
	}
} else if($uri->seg[1] == "ticket") {
	$ticket = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE tid='".$db->escapestring($uri->seg[2])."' AND projectid='".$project['id']."' LIMIT 1")); // Get Ticket info
	if($uri->seg[3] == "delete") {
		if($user->loggedin) {
			$db->query("DELETE FROM ".DBPREFIX."tickets WHERE tid='".$ticket['tid']."' AND projectid='".$project['id']."' LIMIT 1");
			$db->query("DELETE FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."' LIMIT 1");
			header("Location: ".$uri->anchor($project['slug'],'tickets'));
		}
	} elseif($_POST['action'] == "update") {
		$changes = array();
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
		/*if($_POST['close']) {
			$changes[] = "CLOSE";
			$db->query("UPDATE ".DBPREFIX."tickets SET status='0' WHERE id='".$ticket['id']."' LIMIT 1");
		}
		if($_POST['status'] != $ticket['status']) {
			$changes[] = "STATUS:".$_POST['status'].",".$ticket['status'];
		}*/
		if($_POST['ticketaction'] == "markas") {
			$changes[] = "STATUS:".$_POST['markas'].",".$ticket['status'];
			$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['markas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
		} elseif($_POST['ticketaction'] == "close") {
			$changes[] = "CLOSE:".$_POST['closeas'].",".$ticket['status'];
			$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['closeas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
			$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,2,'TICKETCLOSE:".$ticket['id']."',".time().",NOW(),".$project['id'].")");
		} elseif($_POST['ticketaction'] == "reopen") {
			$changes[] = "REOPEN:".$_POST['reopenas'].",".$ticket['status'];
			$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['reopenas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
			$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,3,'TICKETREOPEN:".$ticket['id']."',".time().",NOW(),".$project['id'].")");
		}
		if(count($changes) > 0) {
			$changes = implode('|',$changes);
			$db->query("UPDATE ".DBPREFIX."tickets SET type='".$db->escapestring($_POST['type'])."',
													   assigneeid='".$db->escapestring($_POST['assignto'])."',
													   priority='".$db->escapestring($_POST['priority'])."',
													   severity='".$db->escapestring($_POST['severity'])."',
													   milestoneid='".$db->escapestring($_POST['milestone'])."',
													   versionid='".$db->escapestring($_POST['version'])."',
													   componentid='".$db->escapestring($_POST['component'])."',
													   updated='".time()."'
													   WHERE id='".$ticket['id']."' LIMIT 1");
			$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->uid.",".$ticket['id'].",'".$changes."')");
		}
		header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid']).'?updated');
	} elseif($_POST['action'] == "comment") {
		if($user->loggedin) {
			$db->query("INSERT INTO ".DBPREFIX."ticketcomments VALUES(0,".$user->info->uid.",'".$db->escapestring($_POST['comment'])."',".$ticket['id'].",".time().")");
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid']));
		}
	} elseif($_POST['action'] == "deletecomment") {
		if($user->group->isadmin) {
			$db->query("DELETE FROM ".DBPREFIX."ticketcomments WHERE id='".$db->escapestring($_POST['commentid'])."' LIMIT 1");
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid']));
		}
	} else {
		// View Ticket
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$ticket['milestoneid']."' LIMIT 1")); // Get ticket Milestone info
		$version = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$ticket['versionid']."' LIMIT 1")); // Get ticket Version info
		$component = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$ticket['componentid']."' LIMIT 1")); // Get ticket Component info
		$owner = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$ticket['ownerid']."' LIMIT 1")); // Get ticket Owner info
		$assignee = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$ticket['assigneeid']."' LIMIT 1")); // Get ticket Assignee info
		
		$breadcrumbs[$uri->anchor($project['slug'],'tickets')] = "Tickets";
		//$breadcrumbs[$uri->anchor($project['slug'],'tickets',$milestone['milestone'])] = $milestone['milestone'];
		$breadcrumbs[$uri->anchor($project['slug'],'ticket',$ticket['tid'])] = '#'.$ticket['tid'];
		// Ticket History
		$history = array();
		$fetchhistory = $db->query("SELECT * FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."' ORDER BY id ASC");
		while($info = $db->fetcharray($fetchhistory)) {
			$info['user'] = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$info['userid']."' LIMIT 1"));
			$changes = explode('|',$info['changes']);
			$info['changes'] = array();
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
					$change['from'] = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$change['fromid']."' LIMIT 1"));
					$change['to'] = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$change['toid']."' LIMIT 1"));
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
		// Ticket Comments
		$comments = array();
		$fetchcomments = $db->query("SELECT * FROM ".DBPREFIX."ticketcomments WHERE ticketid='".$ticket['id']."' ORDER BY timestamp DESC");
		while($info = $db->fetcharray($fetchcomments)) {
			$info['author'] = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$info['authorid']."' LIMIT 1"));
			$comments[] = $info;
		}
		include(template('ticket'));
	}
} elseif($uri->seg[1] == "milestone") {
	$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1")); // Get ticket Milestone info
	$milestone['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND milestoneid='".$milestone['id']."'"));
	$milestone['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status <= 0 AND milestoneid='".$milestone['id']."'"));
	$milestone['tickets']['total'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE milestoneid='".$milestone['id']."'"));
	$milestone['tickets']['percent']['closed'] = calculatepercent($milestone['tickets']['closed'],$milestone['tickets']['total']);
	$milestone['tickets']['percent']['open'] = calculatepercent($milestone['tickets']['open'],$milestone['tickets']['total']);
	$breadcrumbs[$uri->anchor($project['slug'],'roadmap')] = "Milestones";
	$breadcrumbs[$uri->anchor($project['slug'],'milestone',$milestone['milestone'])] = $milestone['milestone'];
	include(template('milestone'));
}
?>