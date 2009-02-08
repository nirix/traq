<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array($ticket['summary']. ' (ticket #'.$ticket['tid'].')','Tickets',$project['name']))?></title>
<? include(template('style')); ?> 
</head>
<body>
<? include(template('header')); ?>
	<? include(template('project_nav')); ?>
	<div id="content">
		<? include(template("breadcrumbs")); ?>
		<div id="ticket">
			<div class="date">
				<p title="12/14/08 08:37:54">Opened <?=timesince($ticket['timestamp'])?> ago</p>
				<p title="01/19/09 14:13:28">Last modified <?=($ticket['updated'] ? timesince($ticket['updated']).' ago' : 'Never')?></p>
			</div>
			<h1 class="summary"><?=$ticket['summary']?> <small>(ticket #<?=$ticket['tid']?>)</small></h1>
			<table class="properties">
				<tr>
					<th id="h_owner">Reported by:</th>
					<td headers="h_owner"><?=$owner['username']?></td>
					<th id="h_assignee">Assigned to:</th>
					<td headers="h_assignee"><?=$assignee['username']?></td>
				</tr>
				<tr>
					<th id="h_type">Type:</th>
					<td headers="h_type"><?=tickettype($ticket['type'])?></td>
					<th id="h_priority">Priority:</th>
					<td headers="h_priority"><?=ticketpriority($ticket['priority'])?></td>
				</tr>
				<tr>
					<th id="h_severity">Severity:</th>
					<td headers="h_severity"><?=ticketseverity($ticket['severity'])?></td>
					<th id="h_component">Component:</th>
					<td headers="h_component"><?=$component['name']?></td>
				</tr>
				<tr>
					<th id="h_milestone">Milestone:</th>
					<td headers="h_milestone"><?=$milestone['milestone']?></td>
					<th id="h_version">Version:</th>
					<td headers="h_version"><?=$version['version']?></td>
				</tr>
				<tr>
					<th id="h_status">Status:</th>
					<td headers="h_status"><?=ticketstatus($ticket['status'])?></td>
				</tr>
			</table>
			<div class="description">
				<h3 id="description">Description</h3>
				<p>
					<?=nl2br(stripslashes($ticket['body']))?> 
				</p>
			</div>
		</div>
		<? if($user->group->isadmin or in_array($user->info->uid,$project['managerids'])) { ?>
		<div id="ticket_options">
			<form action="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>" method="post">
				<input type="hidden" name="action" value="update" />
				<fieldset id="properties">
					<legend>Update Ticket</legend>
					<table>
						<tr>
							<th class="col1">Type</th>
							<td class="col2">
								<select name="type" id="type">
									<option value="1"<?=($ticket['type'] == 1 ? ' selected="selected"' : '')?>>Defect</option>
									<option value="2"<?=($ticket['type'] == 2 ? ' selected="selected"' : '')?>>Enhancement</option>
									<option value="3"<?=($ticket['type'] == 3 ? ' selected="selected"' : '')?>>Feature Request</option>
									<option value="4"<?=($ticket['type'] == 4 ? ' selected="selected"' : '')?>>Task</option>
								</select>
							</td>
							<th class="col2">Assign to</th>
							<td>
								<select name="assignto" id="assignto">
									<option value="0"<?=($ticket['assigneeid'] == 0 ? ' selected="selected"' : '')?>> </option>
									<? foreach(projectmanagers($project['id']) as $staff) { ?> 
									<option value="<?=$staff['uid']?>"<?=($staff['uid'] == $ticket['assigneeid'] ? ' selected="selected"' : '')?>><?=$staff['username']?></option>
									<? } ?> 
								</select>
							</td>
						</tr>
						<tr>
							<th class="col1">Priority</th>
							<td>
								<select name="priority" id="priority">
									<option value="5"<?=($ticket['priority'] == 5 ? ' selected="selected"' : '')?>>Highest</option>
									<option value="4"<?=($ticket['priority'] == 4 ? ' selected="selected"' : '')?>>High</option>
									<option value="3"<?=($ticket['priority'] == 3 ? ' selected="selected"' : '')?>>Normal</option>
									<option value="2"<?=($ticket['priority'] == 2 ? ' selected="selected"' : '')?>>Low</option>
									<option value="1"<?=($ticket['priority'] == 1 ? ' selected="selected"' : '')?>>Lowest</option>
								</select>
							</td>
							<th class="col2">Severity</th>
							<td>
								<select name="severity" id="severity">
									<option value="1"<?=($ticket['severity'] == 1 ? ' selected="selected"' : '')?>>Blocker</option>
									<option value="2"<?=($ticket['severity'] == 2 ? ' selected="selected"' : '')?>>Critical</option>
									<option value="3"<?=($ticket['severity'] == 3 ? ' selected="selected"' : '')?>>Major</option>
									<option value="4"<?=($ticket['severity'] == 4 ? ' selected="selected"' : '')?>>Normal</option>
									<option value="5"<?=($ticket['severity'] == 5 ? ' selected="selected"' : '')?>>Minor</option>
									<option value="6"<?=($ticket['severity'] == 6 ? ' selected="selected"' : '')?>>Trivial</option>
								</select>
							</td>
						</tr>
						<tr>
							<th class="col1">Milestone</th>
							<td>
								<select name="milestone" id="milestone">
									<? foreach(projectmilestones($project['id']) as $milestone) { ?>
									<option value="<?=$milestone['id']?>"<?=($ticket['milestoneid'] == $milestone['id'] ? ' selected="selected"' : '')?>><?=$milestone['milestone']?></option>
									<? } ?>
								</select>
							</td>
							<th class="col2">Version</th>
							<td>
								<select name="version" id="version">
									<option<?=($ticket['version'] == 0 ? ' selected="selected"' : '')?> value="0"> </option>
									<? foreach(projectversions($project['id']) as $version) { ?>
									<option value="<?=$version['id']?>"<?=($ticket['versionid'] == $version['id'] ? ' selected="selected"' : '')?>><?=$version['version']?></option>
									<? } ?>
								</select>
							</td>
						</tr>
						<tr>
							<th class="col1">Component</th>
							<td>
								<select name="component" id="component">
									<? foreach(projectcomponents($project['id']) as $component) { ?>
									<option value="<?=$component['id']?>"<?=($ticket['componentid'] == $component['id'] ? ' selected="selected"' : '')?>><?=$component['name']?></option>
									<? } ?>
								</select>
							</td>
							<th class="col2"></th>
							<td></td>
						</tr>
						<tr>
							<th class="col1">Action</th>
							<td>
								<? if($ticket['status'] >= 1) { ?>
								<input type="radio" name="ticketaction" value="markas" /> Mark as <select name="markas" id="markas">
									<option value="1"<?=($ticket['status'] == 1 ? ' selected="selected"' : '')?>>New</option>
									<option value="2"<?=($ticket['status'] == 2 ? ' selected="selected"' : '')?>>Accepted</option>
								</select><br />
								<input type="radio" name="ticketaction" value="close" /> Close as <select name="closeas" id="closeas">
									<option value="0"<?=($ticket['status'] == 0 ? ' selected="selected"' : '')?>>Closed</option>
									<option value="-1"<?=($ticket['status'] == -1 ? ' selected="selected"' : '')?>>Completed</option>
									<option value="-3"<?=($ticket['status'] == -3 ? ' selected="selected"' : '')?>>Fixed</option>
									<option value="-2"<?=($ticket['status'] == -2 ? ' selected="selected"' : '')?>>Rejected</option>
								</select>
								<? } elseif($ticket['status'] <= 0) { ?>
								<input type="radio" name="ticketaction" value="reopen" /> Reopen as <select name="reopenas" id="reopenas">
									<option value="1"<?=($ticket['status'] == 1 ? ' selected="selected"' : '')?>>New</option>
									<option value="2"<?=($ticket['status'] == 2 ? ' selected="selected"' : '')?>>Accepted</option>
								</select>
								<? } ?>
							</td>
							<th class="col2"></th>
							<td><button type="submit">Update</button> <button type="button" onclick="if(confirm('Are you sure you want to delete ticket #'+<?=$ticket['tid']?>)) { window.location='<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'],'delete')?>' }">Delete</button></td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
		<? } ?>
		<div id="history">
			<h3>History</h3>
			<table class="properties">
			<? foreach($history as $info) { ?>
				<tr>
					<th valign="top"><?=date("g:ia d/m/Y OT",$info['timestamp'])?>:</th>
					<td valign="top">
					<? foreach($info['changes'] as $change) { ?>
						<? if($change['type'] == "CREATE") { ?>
						Ticket created by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "COMPONENT") { ?>
						Component changed from <em><?=$change['from']['name']?></em> to <em><?=$change['to']['name']?></em> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "SEVERITY") { ?>
						Severity changed from <em><?=$change['from']?></em> to <em><?=$change['to']?></em> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "TYPE") { ?>
						Type changed from <em><?=$change['from']?></em> to <em><?=$change['to']?></em> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "ASIGNEE") { ?>
						Reassigned to <em><?=$change['to']['username']?></em> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "MILESTONE") { ?>
						Milestone changed from <em><?=$change['from']['milestone']?></em> to <em><?=$change['to']['milestone']?></em> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "CLOSE") { ?>
						Ticket closed as <?=$change['to']?> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "STATUS") { ?>
						Status changed from <em><?=$change['from']?></em> to <em><?=$change['to']?></em> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "PRIORITY") { ?>
						Priority changed from <em><?=$change['from']?></em> to <em><?=$change['to']?></em> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "VERSION") { ?>
						Version changed from <em><?=($change['from']['version'] != '' ? $change['from']['version'] : 'None')?></em> to <em><?=($change['to']['version'] != '' ? $change['to']['version'] : 'None')?></em> by <?=$info['user']['username']?><br />
						<? } else if($change['type'] == "REOPEN") { ?>
						Ticket reopened as <?=$change['to']?> by <?=$info['user']['username']?><br />
						<? } ?>
					<? } ?>
					</td>
				</tr>
			<? } ?>
			</table>
		</div>
	</div>
<? include(template('footer')); ?>
</body>
</html>