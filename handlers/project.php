<?php
/**
 * Traq
 * Copyright (C) 2009 Rainbird Studios
 * Copyright (C) 2009 Jack Polgar
 * All Rights Reserved
 *
 * This software is licensed as described in the file COPYING, which
 * you should have received as part of this distribution.
 *
 * $Id$
 */

// Get the project info
$project = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."projects WHERE slug='".$db->escapestring($uri->seg[0])."' LIMIT 1"));
$project['managerids'] = explode(',',$project['managers']);
$project['desc'] = formattext($project['desc']);
$breadcrumbs[$uri->anchor($project['slug'])] = $project['name'];

($hook = FishHook::hook('project_start')) ? eval($hook) : false;

// Check what page to display
if(!isset($uri->seg[1])) {
	// Project Info page
	($hook = FishHook::hook('project_info')) ? eval($hook) : false;
	$project['tickets']['active'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND projectid='".$project['id']."'")); // Count open tickets
	$project['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status <= 0 AND projectid='".$project['id']."'")); // Count closed tickets
	$project['tickets']['total'] = ($project['tickets']['active']+$project['tickets']['closed']); // Count total tickets
	include(template('project'));
} elseif($uri->seg[1] == "roadmap") {
	include(TRAQPATH."handlers/roadmap.php");
} elseif($uri->seg[1] == "tickets") {
	include(TRAQPATH."handlers/tickets.php");
} else if($uri->seg[1] == "newticket") {
	// Check if user can create tickets
	if(!$user->group->createtickets) {
		include(template('nopermission'));
		exit;
	}
	$breadcrumbs[$uri->anchor($project['slug'],'newticket')] = l("new_ticket");
	
	($hook = FishHook::hook('project_newticket')) ? eval($hook) : false;
	
	if($_POST['action'] == "create") {
		// Check for errors
		$errors = array();
		// Check if Summary is blank
		if($_POST['summary'] == "") {
			$errors['summary'] = l('ERROR_SUMMARY_BLANK');
		}
		// Check if the ticket body is blank
		if($_POST['body'] == "") {
			$errors['body'] = l('ERROR_DESCRIPTION_BLANK');
		}
		// Check if the anti-spam key is valid
		if($_POST['key'] != $_SESSION['key'] && !$user->loggedin) {
			$errors['key'] = l('ERROR_HUMANCHECK_FAILED');
		}
		// Check if the guests name is blank or not
		if(empty($_POST['name']) && !$user->loggedin) {
			$errors['name'] = l('ERROR_NAME_BLANK');
		}
		// Check if the guest name is a registered user [Ticket #53]
		if(!$user->loggedin && $db->numrows($db->query("SELECT username FROM ".DBPREFIX."users WHERE username='".$db->escapestring($_POST['name'])."' LIMIT 1")))
		{
			$errors['name'] = l('GUEST_NAME_REGISTERED');
		}
		
		// Fix the milestone and component values, fixes ticket #19
		if(empty($_POST['milestone'])) {
			$_POST['milestone'] = 0;
		}
		if(empty($_POST['component'])) {
			$_POST['component'] = 0;
		}
		
		// Check with Akismet if its spam or not...
		if($settings->akismetkey != '')
		{
			if($user->loggedin)
			{
				$username = $user->info->username;
			}
			else
			{
				$username = $_POST['name'];
			}
			
			$akismet->setCommentAuthor($username);
			$akismet->setCommentContent($_POST['body']);
			if($akismet->isCommentSpam())
			{
				$errors['akismet'] = 'Your ticket appears to be spam.';
			}
			else
			{
				//exit('ok');
			}
		}
		
		if(!count($errors)) {
			// Insert the ticket into the database
			$ticketid = $project['currenttid']+1;
			$db->query("INSERT INTO ".DBPREFIX."tickets VALUES(
															   0,
															   ".$ticketid.",
															   '".$db->escapestring(htmlspecialchars($_POST['summary']))."',
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
															   '".($user->loggedin ? $user->info->username : $db->escapestring($_POST['name']))."',
															   ".$db->escapestring($_POST['assignto']).",
															   ".time().",
															   0
															   )");
			// Set the guest name if the user is not logged in...
			if(!$user->loggedin) {
				setcookie('guestname',$_POST['name'],time()+9999999,$user->cookie['path'],$user->cookie['domain']);
			}
			// Ticket internal ID
			$internalid = $db->insertid();
			// Update the project currentid field.
			$db->query("UPDATE ".DBPREFIX."projects SET currenttid='".$ticketid."' WHERE id='".$project['id']."' LIMIT 1");
			// Add the CREATE to the ticket history
			$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->id.",'".($user->loggedin ? $user->info->username : $db->escapestring($_POST['name']))."',".$internalid.",'CREATE','')");
			// Add the ticket creation to the timeline
			$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,1,'TICKETCREATE:".$internalid."',".time().",NOW(),".$user->info->id.",'".($user->loggedin ? $user->info->username : $db->escapestring($_POST['name']))."',".$project['id'].")");
			// Redirect to the ticket page...
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticketid));
		} else {
			// oops, there were errors...
			include(template('newticket'));
		}
	} else {
		// Display New Ticket page.
		include(template('newticket'));
	}
} else if($uri->seg[1] == "ticket") {
	// Ticket Page
	$ticket = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE tid='".$db->escapestring($uri->seg[2])."' AND projectid='".$project['id']."' LIMIT 1")); // Get Ticket info
	$ticket['summary'] = stripslashes($ticket['summary']); // Strip the slashes from the summary field
	$ticket['body'] = stripslashes($ticket['body']); // Strip the slashes from the body field
	$ticket['body'] = formattext($ticket['body']); // Format the body field.
	FishHook::hook('projecthandler_ticketpage_start');
	if($uri->seg[3] == "delete") {
		// Delete the ticket...
		if($user->group->isadmin or in_array($user->info->id,$project['managerids'])) { // Check if the user is an admin or project manager
			$db->query("DELETE FROM ".DBPREFIX."tickets WHERE tid='".$ticket['tid']."' AND projectid='".$project['id']."' LIMIT 1"); // Delete from tickets table
			$db->query("DELETE FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."'"); // Delete the ticket history
			$db->query("DELETE FROM ".DBPREFIX."attachments WHERE ticketid='".$ticket['id']."'"); // Delet ethe attachments
			$db->query("DELETE FROM ".DBPREFIX."timeline WHERE data LIKE 'TICKET%:".$ticket['id']."'"); // Delete timeline rows regarding this ticket
			($hook = FishHook::hook('prject_deleteticket')) ? eval($hook) : false;
			header("Location: ".$uri->anchor($project['slug'],'tickets')); // Redirect to the tickets list page
		}
	} elseif($_POST['action'] == "deleteattachment") {
		// Delete attachment
		if($user->group->isadmin or in_array($user->info->id,$project['managerids'])) { // Check if the user is an admin or project manager
			$db->query("DELETE FROM ".DBPREFIX."attachments WHERE id='".$db->escapestring($_POST['attachmentid'])."' LIMIT 1"); // Delete from the attachment table
			($hook = FishHook::hook('project_deleteattachment')) ? eval($hook) : false;
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid'])); // Redirect to the ticket view page
		}
	} elseif($uri->seg[3] == "attachment") {
		// View attachment
		$attachment = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."attachments WHERE id='".$db->escapestring($uri->seg[4])."' AND ticketid='".$ticket['id']."' LIMIT 1")); // Get the attachment
		header("Content-type: ".$attachment['type']); // Set the page content-type
		header("Content-Disposition: attachment; filename=\"".$attachment['name']."\""); // Set the content disposition and filename
		($hook = FishHook::hook('project_viewattachment')) ? eval($hook) : false;
		print(base64_decode($attachment['contents'])); // Print the attachment contents
	} else {
		// Update Ticket
		if($_POST['action'] == "update") {
			$changes = array();
			// Check if the guests name is blank or not
			if(empty($_POST['name']) && !$user->loggedin) {
				$errors[] = "You must enter a name";
			}
			// Check if the anti-spam key is valid
			if($_POST['key'] != $_SESSION['key'] && !$user->loggedin) {
				$errors[] = "Human Check failed";
			}
			// Check if the guest name is a registered user [Ticket #53]
			if(!$user->loggedin && $db->numrows($db->query("SELECT username FROM ".DBPREFIX."users WHERE username='".$db->escapestring($_POST['name'])."' LIMIT 1")))
			{
				$errors['name'] = l('GUEST_NAME_REGISTERED');
			}
			// Check with Akismet if its spam or not...
			if($settings->akismetkey != '')
			{
				if($user->loggedin)
				{
					$username = $user->info->username;
				}
				else
				{
					$username = $_POST['name'];
				}
				
				$akismet->setCommentAuthor($username);
				$akismet->setCommentContent($_POST['comment']);
				if($akismet->isCommentSpam())
				{
					$errors['akismet'] = 'Your comment appears to be spam.';
				}
			}
			// If there are no errors, update the tickets
			if($user->group->updatetickets && !count($errors)) {
				// Ticket Type
				if($_POST['type'] != $ticket['type']) {
					$changes[] = "TYPE:".$_POST['type'].",".$ticket['type'];
				}
				// Assignee
				if($_POST['assignto'] != $ticket['assigneeid']) {
					$changes[] = "ASIGNEE:".$_POST['assignto'].",".$ticket['assigneeid'];
				}
				// Ticket Priority
				if($_POST['priority'] != $ticket['priority']) {
					$changes[] = "PRIORITY:".$_POST['priority'].",".$ticket['priority'];
				}
				// Ticket Severity
				if($_POST['severity'] != $ticket['severity']) {
					$changes[] = "SEVERITY:".$_POST['severity'].",".$ticket['severity'];
				}
				// Milestone
				if($_POST['milestone'] != $ticket['milestoneid']) {
					$changes[] = "MILESTONE:".$_POST['milestone'].",".$ticket['milestoneid'];
				}
				// Version
				if($_POST['version'] != $ticket['versionid']) {
					$changes[] = "VERSION:".$_POST['version'].",".$ticket['versionid'];
				}
				// Component
				if($_POST['component'] != $ticket['componentid']) {
					$changes[] = "COMPONENT:".$_POST['component'].",".$ticket['componentid'];
				}
				// Summary
				if(htmlspecialchars(stripslashes($_POST['summary'])) != stripslashes($ticket['summary'])) {
					$changes[] = "SUMMARY";
				}
				// Ticket Status
				if($_POST['ticketaction'] == "markas") {
					// Mark as something else
					$changes[] = "STATUS:".$_POST['markas'].",".$ticket['status'];
					$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['markas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
				} elseif($_POST['ticketaction'] == "close") {
					// Close the ticket
					$changes[] = "CLOSE:".$_POST['closeas'].",".$ticket['status'];
					$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['closeas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
					$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,2,'TICKETCLOSE:".$ticket['id']."',".time().",NOW(),".$user->info->id.",'".($user->loggedin ? $user->info->username : $db->escapestring($_POST['name']))."',".$project['id'].")");
				} elseif($_POST['ticketaction'] == "reopen") {
					// Reopen the ticket
					$changes[] = "REOPEN:".$_POST['reopenas'].",".$ticket['status'];
					$db->query("UPDATE ".DBPREFIX."tickets SET status='".$db->escapestring($_POST['reopenas'])."' WHERE id='".$ticket['id']."' LIMIT 1");
					$db->query("INSERT INTO ".DBPREFIX."timeline VALUES(0,3,'TICKETREOPEN:".$ticket['id']."',".time().",NOW(),".$user->info->id.",'".($user->loggedin ? $user->info->username : $db->escapestring($_POST['name']))."',".$project['id'].")");
				}
				// Set the guest name if the user is not logged in...
				if(!$user->loggedin) {
					setcookie('guestname',$_POST['name'],time()+9999999,$user->cookie['path'],$user->cookie['domain']);
				}
				// Check if there are changes, if so then update the ticket fields...
				if(count($changes) > 0) {
					($hook = FishHook::hook('project_updateticket')) ? eval($hook) : false;
					$db->query("UPDATE ".DBPREFIX."tickets SET type='".$db->escapestring($_POST['type'])."',
															   summary='".$db->escapestring(htmlspecialchars($_POST['summary']))."',
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
			// Add the change to the ticket history, or the comment...
			if((!empty($_POST['comment']) or count($changes) > 0) && !count($errors)) {
				$changes = implode('|',$changes);
				$db->query("INSERT INTO ".DBPREFIX."tickethistory VALUES(0,".time().",".$user->info->id.",'".($user->loggedin ? $user->info->username : $db->escapestring($_POST['name']))."',".$ticket['id'].",'".$db->escapestring($changes)."','".$db->escapestring($_POST['comment'])."')");
			}
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid']).'?updated'); // Redirect to the ticket view page
		} elseif($uri->seg[3] == "deletecomment") {
			// Delete comment/ticket history..
			if($user->group->isadmin) { // Check is user is an admin
				$db->query("DELETE FROM ".DBPREFIX."tickethistory WHERE id='".$db->escapestring($uri->seg[4])."' LIMIT 1"); // Delete from the ticket history table
				header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid'])); // Redirect to the ticket view page
			}
		}
		// Display Ticket
		$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$ticket['milestoneid']."' LIMIT 1")); // Get ticket Milestone info
		$version = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$ticket['versionid']."' LIMIT 1")); // Get ticket Version info
		$component = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$ticket['componentid']."' LIMIT 1")); // Get ticket Component info
		$owner = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$ticket['ownerid']."' LIMIT 1")); // Get ticket Owner info
		$assignee = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$ticket['assigneeid']."' LIMIT 1")); // Get ticket Assignee info
		
		// Make breadcrumbs
		$breadcrumbs[$uri->anchor($project['slug'],'tickets')] = "Tickets";
		$breadcrumbs[$uri->anchor($project['slug'],'ticket',$ticket['tid'])] = '#'.$ticket['tid'];
		
		($hook = FishHook::hook('project_viewticket')) ? eval($hook) : false;
		
		// Ticket History
		$history = array();
		$fetchhistory = $db->query("SELECT * FROM ".DBPREFIX."tickethistory WHERE ticketid='".$ticket['id']."' ORDER BY id ASC");
		while($info = $db->fetcharray($fetchhistory)) {
			$info['user'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$info['userid']."' LIMIT 1"));
			$changes = explode('|',$info['changes']);
			$info['comment_orig'] = $info['comment'];
			$info['comment'] = formattext($info['comment']);
			$info['changes'] = array();
			($hook = FishHook::hook('project_viewticket_fetchhistory')) ? eval($hook) : false;
			foreach($changes as $change) {
				$parts = explode(':',$change);
				$type = $parts[0];
				$values = explode(',',$parts[1]);
				$change = array();
				$change['type'] = $type;
				$change['toid'] = $values[0];
				$change['fromid'] = $values[1];
				// Check the change type
				if($type == "COMPONENT") {
					// Component Change
					$change['from'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$change['fromid']."' LIMIT 1")); // From value
					$change['to'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$change['toid']."' LIMIT 1")); // To value
				} elseif($type == "SEVERITY") {
					// Severity Change
					$change['from'] = ticketseverity($change['fromid']); // From value
					$change['to'] = ticketseverity($change['toid']); // To value
				} else if($type == "TYPE") {
					// Type Change
					$change['from'] = tickettype($change['fromid']); // From value
					$change['to'] = tickettype($change['toid']); // To value
				} else if($type == "ASIGNEE") {
					// Asignee Change
					$change['from'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$change['fromid']."' LIMIT 1")); // From value
					$change['to'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$change['toid']."' LIMIT 1")); // To value
				} else if($type == "MILESTONE") {
					// Milestone Change
					$change['from'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$change['fromid']."' LIMIT 1")); // From value
					$change['to'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE id='".$change['toid']."' LIMIT 1")); // To value
				} else if($type == "STATUS") {
					// Status Change
					$change['from'] = ticketstatus($change['fromid']); // From value
					$change['to'] = ticketstatus($change['toid']); // To value
				} else if($type == "PRIORITY") {
					// Priority Change
					$change['from'] = ticketpriority($change['fromid']); // From value
					$change['to'] = ticketpriority($change['toid']); // To value
				} else if($type == "VERSION") {
					// Version Change
					$change['from'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$change['fromid']."' LIMIT 1")); // From value
					$change['to'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."versions WHERE id='".$change['toid']."' LIMIT 1")); // To value
				} else if($type == "REOPEN") {
					// Ticket Reopen
					$change['from'] = ticketstatus($change['fromid']); // From value
					$change['to'] = ticketstatus($change['toid']); // To value
				} else if($type == "CLOSE") {
					// Ticket Close
					$change['from'] = ticketstatus($change['fromid']); // From value
					$change['to'] = ticketstatus($change['toid']); // To value
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
			($hook = FishHook::hook('project_viewticket_fetchattachments')) ? eval($hook) : false;
			$attachments[] = $info;
		}
		// Attach File
		if($_POST['action'] == "attachfile") {
			if($user->loggedin) { // Check if user is logged in
				if(!empty($_FILES['file']['name'])) {
					($hook = FishHook::hook('project_attachfile')) ? eval($hook) : false;
					$db->query("INSERT INTO ".DBPREFIX."attachments VALUES(0,'".$db->escapestring($_FILES['file']['name'])."','".base64_encode(file_get_contents($_FILES['file']['tmp_name']))."','".$_FILES['file']['type']."',".time().",".$user->info->id.",'".$user->info->username."',".$ticket['id'].",".$project['id'].")");
				}
			}
			header("Location: ".$uri->anchor($project['slug'],'ticket',$ticket['tid'])); // Redirect to ticket view page
		}
		($hook = FishHook::hook('project_viewticket_end')) ? eval($hook) : false;
		include(template('ticket')); // Fetch view ticket template
	}
} elseif($uri->seg[1] == "milestone") {
	// Milestone Page
	$milestone = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."milestones WHERE milestone='".$uri->seg[2]."' AND project='".$project['id']."' LIMIT 1")); // Get ticket Milestone info
	$milestone['desc'] = formattext($milestone['desc']); // Format the milestone description field
	$milestone['tickets']['open'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status >= 1 AND milestoneid='".$milestone['id']."'")); // Count open tickets
	$milestone['tickets']['closed'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE status <= 0 AND milestoneid='".$milestone['id']."'")); // Count closed tickets
	$milestone['tickets']['total'] = $db->numrows($db->query("SELECT projectid,status FROM ".DBPREFIX."tickets WHERE milestoneid='".$milestone['id']."'")); // Count total tickets
	$milestone['tickets']['percent']['closed'] = calculatepercent($milestone['tickets']['closed'],$milestone['tickets']['total']); // Calculate closed tickets percent
	$milestone['tickets']['percent']['open'] = calculatepercent($milestone['tickets']['open'],$milestone['tickets']['total']); // Calculate open tickets percent
	// Breadcrumbs
	$breadcrumbs[$uri->anchor($project['slug'],'roadmap')] = "Milestones";
	$breadcrumbs[$uri->anchor($project['slug'],'milestone',$milestone['milestone'])] = $milestone['milestone'];
	
	($hook = FishHook::hook('project_milestoneinfo')) ? eval($hook) : false;
	
	include(template('milestone')); // Fetch view milestone page
} elseif($uri->seg[1] == "timeline") {
	($hook = FishHook::hook('project_timeline_start')) ? eval($hook) : false;
	// Timeline Page
	$dates = array();
	$fetchdays = $db->query("SELECT DISTINCT YEAR(date) AS 'year', MONTH(date) AS 'month', DAY(date) AS 'day', date, timestamp FROM ".DBPREFIX."timeline WHERE projectid='".$project['id']."' GROUP BY YEAR(date), MONTH(date), DAY(date) ORDER BY date DESC"); // Fetch the days...
	while($dateinfo = $db->fetcharray($fetchdays)) {
		$row = array();
		$row['date'] = $dateinfo['date'];
		$row['timestamp'] = $dateinfo['timestamp'];
		$row['rows'] = array();
		$date = explode('-',$row['date']);
		$row['timestamp'] = mktime(0,0,0,$date[1],$date[2],$date[0]);
		$fetchrows = $db->query("SELECT * FROM ".DBPREFIX."timeline WHERE projectid='".$project['id']."' AND date='".$dateinfo['date']."' ORDER BY timestamp DESC"); // Fetch timeline rows
		while($rowinfo = $db->fetcharray($fetchrows)) {
			$parts = explode(':',$rowinfo['data']); // Explode the timeline data field
			$rowinfo['type'] = $parts[0]; // Set the row type
			$rowinfo['user'] = $db->fetcharray($db->query("SELECT id,username FROM ".DBPREFIX."users WHERE id='".$rowinfo['userid']."' LIMIT 1")); // Get the user info
			// Check the type, and set the info for that specified type
			if($parts[0] == "TICKETCREATE" or $parts[0] == "TICKETCLOSE" or $parts[0] == "TICKETREOPEN") {
				// Ticket Open, Close and Reopen
				$rowinfo['ticket'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."tickets WHERE id='".$parts[1]."' LIMIT 1"));
				$rowinfo['ticket']['summary'] = stripslashes($rowinfo['ticket']['summary']);
			}
			($hook = FishHook::hook('project_timeline_fetchrows')) ? eval($hook) : false;
			$row['rows'][] = $rowinfo;
		}
		$dates[] = $row;
	}
	
	// Breadcrumbs
	$breadcrumbs[$uri->anchor($projet['slug'],'timeline')] = "Timeline";
	
	($hook = FishHook::hook('project_timeline_end')) ? eval($hook) : false;
	
	include(template('timeline')); // Fetch the timeline template
} elseif($uri->seg[1] == "changelog") {
	($hook = FishHook::hook('project_changelog_start')) ? eval($hook) : false;
	// Change Log Page
	$milestones = array();
	$fetchmilestones = $db->query("SELECT * FROM ".DBPREFIX."milestones WHERE project='".$project['id']."' AND completed != 0 ORDER BY milestone DESC"); // Fetch the milestones
	while($info = $db->fetcharray($fetchmilestones)) {
		$info['tickets'] = array();
		$fetchtickets = $db->query("SELECT * FROM ".DBPREFIX."tickets WHERE projectid='".$project['id']."' AND milestoneid='".$info['id']."' AND status <= 0 AND status != -2 ORDER BY updated ASC"); // Fetch the tickets
		while($ticketinfo = $db->fetcharray($fetchtickets)) {
			$ticketinfo['summary'] = stripslashes($ticketinfo['summary']); // Strip the slashes from the summary field
			$ticketinfo['body'] = stripslashes($ticketinfo['body']); // Strip the slashes from the body field
			$ticketinfo['component'] = $db->fetcharray($db->query("SELECT * FROM ".DBPREFIX."components WHERE id='".$ticketinfo['componentid']."' LIMIT 1")); // Get the component info
			FishHook::hook('projecthandler_changelog_fetchmilestones_fetchchanges');
			$info['tickets'][] = $ticketinfo;
		}
		($hook = FishHook::hook('project_changelog_fetchrows')) ? eval($hook) : false;
		$milestones[] = $info;
	}
	
	// Breadcrumbs
	$breadcrumbs[$uri->anchor($project['slug'],'changelog')] = "Change Log";
	
	($hook = FishHook::hook('project_changelog_end')) ? eval($hook) : false;
	
	include(template('changelog')); // Fetch the changelog template
} elseif($uri->seg[1] == "source") {
	// Browse Source
	include(TRAQPATH.'handlers/browsesvn.php');
} elseif($uri->seg[1] == "feeds") {
	// Feed (RSS/Atom/etc)
	include(TRAQPATH.'handlers/feeds.php');
}
?>