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
				<p>Opened <?=timesince($ticket['timestamp'])?> ago</p>
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
					<td headers="h_assignee"><?=$assignee['login']?></td>
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
		
		<? require(template('footer')); ?>
	</body>
</html>