<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=buildtitle(array($ticket['summary']. ' ('.l('ticket_x',$ticket['tid']).')',l('tickets'),$project['name']))?></title>
<? include(template('headerinc')); ?> 
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
			<h1 class="summary"><?=$ticket['summary']?> <small>(<?=l('ticket_x',$ticket['tid'])?>)</small> <? if($user->group->isadmin or in_array($user->info->id,$project['managerids'])) { ?>
				<input type="button" onclick="if(confirm('<?=l('delete_ticket_confirm',$ticket['tid'])?>')) { window.location='<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'],'delete')?>' }" value="<?=l('delete')?>" />
				<? } ?></h1>
			<table class="properties">
				<tr>
					<th id="h_owner"><?=l('reported_by')?>:</th>
					<td headers="h_owner"><?=$ticket['ownername']?></td>
					<th id="h_assignee"><?=l('assigned_to')?>:</th>
					<td headers="h_assignee"><?=$assignee['username']?></td>
				</tr>
				<tr>
					<th id="h_type"><?=l('type')?>:</th>
					<td headers="h_type"><?=tickettype($ticket['type'])?></td>
					<th id="h_priority"><?=l('priority')?>:</th>
					<td headers="h_priority"><?=ticketpriority($ticket['priority'])?></td>
				</tr>
				<tr>
					<th id="h_severity"><?=l('severity')?>:</th>
					<td headers="h_severity"><?=ticketseverity($ticket['severity'])?></td>
					<th id="h_component"><?=l('component')?>:</th>
					<td headers="h_component"><?=$component['name']?></td>
				</tr>
				<tr>
					<th id="h_milestone"><?=l('milestone')?>:</th>
					<td headers="h_milestone"><?=$milestone['milestone']?></td>
					<th id="h_version"><?=l('version')?>:</th>
					<td headers="h_version"><?=$version['version']?></td>
				</tr>
				<tr>
					<th id="h_status"><?=l('status')?>:</th>
					<td headers="h_status"><?=ticketstatus($ticket['status'])?></td>
				</tr>
			</table>
			<div class="description">
				<h3 id="description"><?=l('description')?></h3>
				<p>
					<?=($ticket['body'])?> 
				</p>
				<h3><?=l('attachments')?></h3>
				<p id="attachments">
					<ul>
					<? foreach($attachments as $attachment) { ?>
						<li>
							<? if($user->group->isadmin or in_array($user->info->id,$project['managerids'])) { ?><form action="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>" method="post"><? } ?>
							<strong><a href="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'],'attachment',$attachment['id'])?>"><?=$attachment['name']?></a></strong> added by <?=$attachment['ownername']?> <?=timesince($attachment['timestamp'])?> ago.
							<? if($user->group->isadmin or in_array($user->info->id,$project['managerids'])) { ?><input type="hidden" name="action" value="deleteattachment" /><input type="hidden" name="attachmentid" value="<?=$attachment['id']?>" /><input type="submit" value="<?=l('delete')?>" /></form><? } ?>
						</li>
					<? } ?>
					</ul>
				</p>
				<? if($user->loggedin) { ?>
				<p>
					<form action="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="attachfile" />
						<label>Attach File: <input type="file" name="file" /> <input type="submit" value="<?=l('attach')?>" /></label>
					</form>
				</p>
				<? } ?>
			</div>
		</div>
		<h2><?=l('change_history')?></h2>
		<div id="history">
			<? foreach($history as $info) { ?>
				<div class="change">
					<h3><a name="comment:<?=++$historyid?>"></a><?=timesince($info['timestamp'])?> ago by <?=$info['username']?></h3>
					
					<span class="inlinebuttons">
						<? if($user->group->updatetickets) { ?>
						<input type="button" value="<?=l('reply')?>" onclick="document.location='<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>?replyto=<?=$historyid?>'" />
						<? } ?>
						<? if($user->group->isadmin or in_array($user->info->id,$project['managerids'])) { ?>
						<input type="button" value="<?=l('delete')?>" onclick="if(confirm('<?=l('delete_comment_confirm')?>')) { document.location='<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'],'deletecomment',$info['id'])?>'; }" />
						<? } ?>
					</span>
					
					<? if($info['changes'][0]['type'] != '') { ?>
					<ul class="changes">
					<? foreach($info['changes'] as $change) { ?>
						<? if($change['type'] == "CREATE") { ?>
						<li>Ticket created by <?=$info['username']?></li>
						<? } else if($change['type'] == "COMPONENT") { ?>
						<li>Component changed from <em><?=$change['from']['name']?></em> to <em><?=$change['to']['name']?></em></li>
						<? } else if($change['type'] == "SEVERITY") { ?>
						<li>Severity changed from <em><?=$change['from']?></em> to <em><?=$change['to']?></em></li>
						<? } else if($change['type'] == "TYPE") { ?>
						<li>Type changed from <em><?=$change['from']?></em> to <em><?=$change['to']?></em></li>
						<? } else if($change['type'] == "ASIGNEE") { ?>
						<li>Reassigned to <em><?=(empty($change['to']['username']) ? 'No one' : $change['to']['username'])?></em></li>
						<? } else if($change['type'] == "MILESTONE") { ?>
						<li>Milestone changed from <em><?=$change['from']['milestone']?></em> to <em><?=$change['to']['milestone']?></em></li>
						<? } else if($change['type'] == "CLOSE") { ?>
						<li>Ticket closed as <?=$change['to']?> by <?=$info['username']?></li>
						<? } else if($change['type'] == "STATUS") { ?>
						<li>Status changed from <em><?=$change['from']?></em> to <em><?=$change['to']?></em></li>
						<? } else if($change['type'] == "PRIORITY") { ?>
						<li>Priority changed from <em><?=$change['from']?></em> to <em><?=$change['to']?></em></li>
						<? } else if($change['type'] == "VERSION") { ?>
						<li>Version changed from <em><?=($change['from']['version'] != '' ? $change['from']['version'] : 'None')?></em> to <em><?=($change['to']['version'] != '' ? $change['to']['version'] : 'None')?></em></li>
						<? } else if($change['type'] == "REOPEN") { ?>
						<li>Ticket reopened as <?=$change['to']?> by <?=$info['username']?></li>
						<? } else if($change['type'] == "SUMMARY") { ?>
						<li>Summary changed by <?=$info['username']?></li>
						<? } ?>
					<? } ?>
					</ul>
					<? } ?>
					<? if($info['comment'] != "") { ?>
					<div class="comment">
						<?=$info['comment']?> 
					</div>
					<? } ?>
				</div>
			<? } ?>
		</div>
		<? if($user->group->updatetickets) { ?>
		<h2><?=l('update_ticket')?></h2>
		<div id="update_ticket">
			<form action="<?=$uri->anchor($project['slug'],'ticket',$ticket['tid'])?>" method="post">
				<input type="hidden" name="action" value="update" />
				<textarea name="comment">
<? if(isset($_REQUEST['replyto'])) { ?>
[quote=<?=$history[$_REQUEST['replyto']-1]['username']?>]<?=stripslashes($history[$_REQUEST['replyto']-1]['comment_orig'])?>[/quote]
<? } ?></textarea>
				<? if($user->group->updatetickets) { ?>
				<fieldset id="properties">
					<legend><?=l('change_properties')?></legend>
					<table>
						<tr>
							<th class="col1"><?=l('type')?></th>
							<td class="col2">
								<select name="type" id="type">
									<? foreach(gettypes() as $type) { ?>
									<option value="<?=$type['id']?>"<?=($type['id']==$ticket['type'] ? ' selected="selected"' : '')?>><?=$type['name']?></option>
									<? } ?>
								</select>
							</td>
							<th class="col2"><?=l('assign_to')?></th>
							<td>
								<select name="assignto" id="assignto">
									<option value="0"<?=($ticket['assigneeid'] == 0 ? ' selected="selected"' : '')?>> </option>
									<? foreach(projectmanagers($project['id']) as $staff) { ?> 
									<option value="<?=$staff['id']?>"<?=($staff['id'] == $ticket['assigneeid'] ? ' selected="selected"' : '')?>><?=$staff['username']?></option>
									<? } ?> 
								</select>
							</td>
						</tr>
						<tr>
							<th class="col1"><?=l('priority')?></th>
							<td>
								<select name="priority" id="priority">
									<? foreach(getpriorities() as $priority) { ?>
									<option value="<?=$priority['id']?>"<?=($priority['id']==$ticket['priority'] ? ' selected="selected"' : '')?>><?=$priority['name']?></option>
									<? } ?>
								</select>
							</td>
							<th class="col2"><?=l('severity')?></th>
							<td>
								<select name="severity" id="severity">
									<? foreach(getseverities() as $severity) { ?>
									<option value="<?=$severity['id']?>"<?=($severity['id']==$ticket['severity'] ? ' selected="selected"' : '')?>><?=$severity['name']?></option>
									<? } ?>
								</select>
							</td>
						</tr>
						<tr>
							<th class="col1"><?=l('milestone')?></th>
							<td>
								<select name="milestone" id="milestone">
									<? if($milestone['completed']>0) { ?>
									<option value="<?=$milestone['id']?>"selected="selected"><?=$milestone['milestone']?></option>
									<? } ?>
									<? foreach(projectmilestones($project['id']) as $milestone) { ?>
									<option value="<?=$milestone['id']?>"<?=($ticket['milestoneid'] == $milestone['id'] ? ' selected="selected"' : '')?>><?=$milestone['milestone']?></option>
									<? } ?>
								</select>
							</td>
							<th class="col2"><?=l('version')?></th>
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
							<th class="col1"><?=l('component')?></th>
							<td>
								<select name="component" id="component">
									<? foreach(projectcomponents($project['id']) as $component) { ?>
									<option value="<?=$component['id']?>"<?=($ticket['componentid'] == $component['id'] ? ' selected="selected"' : '')?>><?=$component['name']?></option>
									<? } ?>
								</select>
							</td>
							<th class="col2"><?=l('summary')?></th>
							<td><input type="text" name="summary" value="<?=$ticket['summary']?>" /></td>
						</tr>
						<tr>
							<th class="col1"><?=l('action')?></th>
							<td>
								<? if($ticket['status'] >= 1) { ?>
								<input type="radio" name="ticketaction" value="markas" /> <?=l('mark_as')?> <select name="markas" id="markas">
									<?
									foreach(getstatustypes() as $type) {
										if($type['id']>0) {
									?>
									<option value="<?=$type['id']?>"<?=($ticket['status'] == $type['id'] ? ' selected="selected"' : '')?>><?=$type['name']?></option>
									<?
										}
									}
									?>
								</select><br />
								<input type="radio" name="ticketaction" value="close" /> <?=l('close_as')?> <select name="closeas" id="closeas">
									<?
									foreach(getstatustypes('name','asc') as $type) {
										if($type['id']<=0) {
									?>
									<option value="<?=$type['id']?>"<?=($ticket['status'] == $type['id'] ? ' selected="selected"' : '')?>><?=$type['name']?></option>
									<?
										}
									}
									?>
								</select>
								<? } elseif($ticket['status'] <= 0) { ?>
								<input type="radio" name="ticketaction" value="reopen" /> <?=l('reopen_as')?> <select name="reopenas" id="reopenas">
									<?
									foreach(getstatustypes() as $type) {
										if($type['id']>0) {
									?>
									<option value="<?=$type['id']?>"<?=($ticket['status'] == $type['id'] ? ' selected="selected"' : '')?>><?=$type['name']?></option>
									<?
										}
									}
									?>
								</select>
								<? } ?>
							</td>
							<th class="col2"></th>
						</tr>
					</table>
				</fieldset>
			<? if(!$user->loggedin) { ?>
			<fieldset>
				<legend><?=l('your_name')?></legend>
				<input type="text" name="name" value="<?=$_COOKIE['guestname']?>" />
			</fieldset>
			<fieldset>
				<legend><?=l('human_check')?></legend>
				<table>
					<tr>
						<td><img src="<?=$uri->anchor()?>keyimg.php" /></td>
						<td><input type="text" name="key" /></td>
					</tr>
				</table>
			</fieldset>
			<? } ?>
				<? } ?>
				<input type="submit" value="<?=l('update')?>" />
			</form>
		</div>
		<? } ?>
<? include(template('footer')); ?>
</body>
</html>