<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=$project['name']?> / <?=l('tickets')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<? require(template('breadcrumbs')); ?>
		
		<div id="ticket">
			<div class="date">
				<p>Opened <?=timesince($ticket['created'])?> ago</p>
				<p>Last modified <?=($ticket['updated'] ? timesince($ticket['updated']).' ago' : 'Never')?></p>
			</div>
			<h1 class="summary"><?=$ticket['summary']?> <small>(<?=l('ticket_x',$ticket['ticket_id'])?>)</small> <? if($user->group['is_admin'] or in_array($user->info['id'],$project['managers'])) { ?>
				<input type="button" onclick="if(confirm('<?=l('delete_ticket_confirm',$ticket['ticket_id'])?>')) { window.location='<?=$uri->anchor($project['slug'],'ticket',$ticket['ticket_id'],'delete')?>' }" value="<?=l('delete')?>" />
				<? } ?></h1>
			<table class="properties">
				<tr>
					<th id="h_owner"><?=l('reported_by')?>:</th>
					<td headers="h_owner"><?=$ticket['user_name']?></td>
					<th id="h_assignee"><?=l('assigned_to')?>:</th>
					<td headers="h_assignee"><?=$ticket['assignee']['login']?></td>
				</tr>
				<tr>
					<th id="h_type"><?=l('type')?>:</th>
					<td headers="h_type"><?=ticket_type($ticket['type'])?></td>
					<th id="h_priority"><?=l('priority')?>:</th>
					<td headers="h_priority"><?=ticket_priority($ticket['priority'])?></td>
				</tr>
				<tr>
					<th id="h_severity"><?=l('severity')?>:</th>
					<td headers="h_severity"><?=ticket_severity($ticket['severity'])?></td>
					<th id="h_component"><?=l('component')?>:</th>
					<td headers="h_component"><?=$ticket['component']['name']?></td>
				</tr>
				<tr>
					<th id="h_milestone"><?=l('milestone')?>:</th>
					<td headers="h_milestone"><?=$ticket['milestone']['milestone']?></td>
					<th id="h_version"><?=l('version')?>:</th>
					<td headers="h_version"><?=$version['version']?></td>
				</tr>
				<tr>
					<th id="h_status"><?=l('status')?>:</th>
					<td headers="h_status"><?=ticket_status($ticket['status'])?></td>
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
					<? foreach($ticket['attachments'] as $attachment) { ?>
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
		
		<? if($user->group['update_tickets']) { ?>
		<form action="<?=$uri->geturi()?>" method="post">
		<input type="hidden" name="update" value="1" />
		<div id="update_ticket">
			<h2><?=l('update_ticket')?></h2>
			<fieldset class="properties">
				<legend><?=l('ticket_properties')?></legend>
				<table class="properties">
					<tr>
						<td colspan="4"><textarea name="comment"></textarea></td>
					</tr>
					<tr>
						<th class="col1"><?=l('type')?></th>
						<td>
							<select name="type">
								<? foreach(ticket_types() as $type) { ?>
								<option value="<?=$type['id']?>"<?=iif($type['id']==$ticket['type'],' selected="selected"')?>><?=$type['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('assigned_to')?></thd>
						<td>
							<select name="assign_to">
								<option value="0"></option>
								<? foreach(project_managers() as $manager) { ?>
								<option value="<?=$manager['id']?>"<?=iif($manager['id']==$ticket['assigned_to'],' selected="selected"')?>><?=$manager['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('priority')?></th>
						<td>
							<select name="priority">
								<? foreach(ticket_priorities() as $priority) { ?>
								<option value="<?=$priority['id']?>"<?=iif($priority['id']==$ticket['priority'],' selected="selected"')?>><?=$priority['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('severity')?></th>
						<td>
							<select name="severity">
								<? foreach(ticket_severities() as $severity) { ?>
								<option value="<?=$severity['id']?>"<?=iif($severity['id']==$ticket['severity'],' selected="selected"')?>><?=$severity['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('milestone')?></th>
						<td>
							<select name="milestone">
								<? foreach(project_milestones() as $milestone) { ?>
								<option value="<?=$milestone['id']?>"<?=iif($milestone['id']==$ticket['milestone_id'],' selected="selected"')?>><?=$milestone['milestone']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('version')?></th>
						<td>
							<select name="version">
								<option value="0"></option>
								<? foreach(project_versions() as $version) { ?>
								<option value="<?=$version['id']?>"<?=iif($version['id']==$ticket['version_id'],' selected="selected"')?>><?=$version['version']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('component')?></th>
						<td>
							<select name="component">
								<? foreach(project_components() as $component) { ?>
								<option value="<?=$component['id']?>"<?=iif($component['id']==$ticket['component_id'],' selected="selected"')?>><?=$component['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('summary')?></th>
						<td><input type="text" name="summary" value="<?=$ticket['summary']?>" /></td>
					</tr>
					<tr>
						<th class="col1"><?=l('action')?></th>
						<td>
							<? if(!$ticket['closed']) { ?>
							<input type="radio" name="action" value="mark" checked="checked" /> <?=l('mark_as')?> <select name="mark_as">
								<? foreach(ticket_status_list() as $status) { ?>
									<option value="<?=$status['id']?>"<?=($status['id']==$ticket['status'] ? ' selected="selected"' :'')?>><?=$status['name']?></option>
								<? } ?>
							</select>
							<br />
							<input type="radio" name="action" value="close" /> <?=l('close_as')?> <select name="close_as">
								<? foreach(ticket_status_list(0) as $status) { ?>
									<option value="<?=$status['id']?>"<?=($status['id']==$ticket['status'] ? ' selected="selected"' :'')?>><?=$status['name']?></option>
								<? } ?>
							</select>
							<? } else if($ticket['closed']) { ?>
							<input type="radio" name="action" value="reopen" /> <?=l('reopen_as')?>
							<? } ?>
						</td>
						<th class="col2"><?=l('private_ticket')?></th>
						<td><input type="checkbox" name="private" value="1" /></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="<?=l('update')?>" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
		</form>
		<? } ?>
		<? require(template('footer')); ?>
	</body>
</html>