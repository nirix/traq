<?php
/**
 * Traq
 * Copyright (c) 2009 Rainbird Studios
 * $Id$
 */

// Get the project info
$project = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($uri->seg[0])."' LIMIT 1"));
$project['managerids'] = explode(',',$project['managers']);

// Check what page to display
if(!isset($uri->seg[1])) {
	// Project Info page
	include(template('project'));
} elseif($uri->seg[1] == "roadmap") {
	// Roadmap Page
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project=".$project['id']." ORDER BY due ASC");
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
	if($uri->seg[2]) { // Open or Closed tickets.
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1"));
		if($uri->seg[3] == "open") {
			$status = "status >= 1";
		} elseif($uri->seg[3] == "closed") {
			$status = "status <= 0";
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
	} else { // All Tickets
		// Get Tickets
		$tickets = array();
		$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE projectid='".$project['id']."' ORDER BY priority DESC");
		while($info = $db->fetcharray($fetchtickets)) {
			$info['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$info['componentid']."' LIMIT 1")); // Get Component info
			$info['owner'] = $user->getinfo($info['ownerid']); // Get owner info
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
	if($_POST['action'] == "create") {
		$errors = array();
		if($_POST['summary'] == "") {
			$errors['summary'] = "Summary cannot be blank";
		}
		if($_POST['body'] == "") {
			$errors['body'] = "You must enter a description.";
		}
		
		if(!count($errors)) {
			$db->query("INSERT INTO ".DBPREFIX."tickets VALUES(
															   0,
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
			$ticketid = $db->insertid();
			$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->uid.",".$ticketid.",'CREATE')");
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticketid));
		} else {
			include(template('newticket'));
		}
	} else {
		include(template('newticket'));
	}
} else if($uri->seg[1] == "ticket") {
	$ticket = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE id='".$db->escapestring($uri->seg[2])."' AND projectid='".$project['id']."' LIMIT 1")); // Get Ticket info
	if($uri->seg[3] == "delete") {
		if($user->loggedin) {
			$db->query("DELETE FROM ".DBPREFIX."tickets WHERE id='".$ticket['id']."' LIMIT 1");
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
		if($_POST['close']) {
			$changes[] = "CLOSE";
			$db->query("UPDATE ".DBPREFIX."tickets SET status='0' WHERE id='".$ticket['id']."' LIMIT 1");
		}
		if($_POST['status'] != $ticket['status']) {
			$changes[] = "STATUS:".$_POST['status'].",".$ticket['status'];
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
													   status='".$db->escapestring($_POST['status'])."',
													   updated='".time()."'
													   WHERE id='".$ticket['id']."' LIMIT 1");
			$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->uid.",".$ticket['id'].",'".$changes."')");
		}
		header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['id']).'?updated');
	} else {
		// View Ticket
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$ticket['milestoneid']."' LIMIT 1")); // Get ticket Milestone info
		$version = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$ticket['versionid']."' LIMIT 1")); // Get ticket Version info
		$component = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$ticket['componentid']."' LIMIT 1")); // Get ticket Component info
		$owner = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$ticket['ownerid']."' LIMIT 1")); // Get ticket Owner info
		$assignee = $db->fetcharray($db->query("SELECT uid,username FROM ".DBPREFIX."users WHERE uid='".$ticket['assigneeid']."' LIMIT 1")); // Get ticket Assignee info
		// Ticket History
		$history = array();
		$gethistory = $db->query("SELECT * FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."' ORDER BY id ASC");
		while($info = $db->fetcharray($gethistory)) {
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
				}
				$info['changes'][] = $change;
			}
			$history[] = $info;
		}
		include(template('ticket'));
	}
}
?>