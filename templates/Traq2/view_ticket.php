<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo l('tickets')?> / <?php echo $ticket['summary']?> (<?php echo l('ticket_x',$ticket['ticket_id'])?>)</title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<div id="ticket">
			<div class="date">
				<p><?php echo l('opened_x_ago',timesince($ticket['created']))?></p>
				<p><?php echo l('last_updated_'.($ticket['updated'] ? 'x_ago' : 'never'),timesince($ticket['updated']))?></p>
			</div>
			<h1 class="summary"><?php echo $ticket['summary']?> <small>(<?php echo l('ticket_x',$ticket['ticket_id'])?>)</small> <?php if($user->group['is_admin'] or in_array($user->info['id'],$project['managers'])) { ?>
				<input type="button" onclick="if(confirm('<?php echo l('delete_ticket_confirm',$ticket['ticket_id'])?>')) { window.location='<?php echo $uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'],'delete')?>' }" value="<?php echo l('delete')?>" />
				<?php } ?></h1>
			<div class="ticket_properties">
			<?php foreach($ticket_properties as $property) { ?>
				<div class="property">
					<span><?php echo $property['label']?></span>
					<?php echo $property['value']?>
				</div>
			<?php } ?>
				<?php ($hook = FishHook::hook('template_view_ticket_properties')) ? eval($hook) : false; ?>
			</div>
			<div class="clear"></div>
			<div class="description">
				<h3 id="description"><?php echo l('description')?></h3>
				<p>
					<?php echo formattext($ticket['body'],true)?> 
				</p>
				<h3><?php echo l('attachments')?></h3>
				<p id="attachments">
					<ul>
					<?php foreach($ticket['attachments'] as $attachment) { ?>
						<li>
							<?php if($user->group['is_admin'] or in_array($user->info['id'],$project['managers'])) { ?><form action="<?php echo $uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'])?>" method="post"><?php } ?>
							<strong><a href="<?php echo $uri->anchor($project['slug'],'attachment-'.$attachment['id'])?>"><?php echo $attachment['name']?></a></strong> added by <?php echo $attachment['owner_name']?> <?php echo timesince($attachment['uploaded'])?> ago.
							<?php if($user->group['is_admin'] or in_array($user->info['id'],$project['managers'])) { ?><input type="hidden" name="action" value="delete_attachment" /><input type="hidden" name="attach_id" value="<?php echo $attachment['id']?>" /><input type="submit" value="<?php echo l('delete')?>" /></form><?php } ?>
						</li>
					<?php } ?>
					</ul>
				</p>
				<?php if($user->group['add_attachments']) { ?>
				<p>
					<form action="<?php echo $uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'])?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="attach_file" />
						<label><?php echo l('attach_file')?>: <input type="file" name="file" /> <input type="submit" value="<?php echo l('attach')?>" /></label>
					</form>
				</p>
				<?php } ?>
			</div>
		</div>
		<?php if($user->loggedin) { ?>
		<div align="right"><a href="<?php echo $uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'],'watch')?>"><?php echo l(iif(is_subscribed('ticket',$ticket['id']),'Unwatch','Watch').'_this_ticket')?></a></div>
		<?php } ?>
		
		<h2><?php echo l('ticket_history')?></h2>
		<div id="ticket_history">
			<?php foreach($ticket['changes'] as $change) { ?>
			<div class="ticket_prop_change">
				<h3><?php echo timesince($change['timestamp'],true)?> ago by <?php echo $change['user_name']?></h3>
				<div class="ticket_change_actions">
					<?php if($user->group['is_admin'] or in_array($user->info['id'],$project['managers'])) { ?>
					<form action="<?php echo $uri->geturi()?>" method="post">
						<input type="hidden" name="action" value="delete_comment" />
						<input type="hidden" name="comment" value="<?php echo $change['id']?>" />
						<input type="submit" value="<?php echo l('delete')?>" />
					</form>
					<?php } ?>
				</div>
				<?php if(count($change['changes']) > 0) { ?>
				<ul class="ticket_change_list">
					<?php foreach($change['changes'] as $row) { ?>
					<li><?php echo l('ticket_history_'.$row->property.iif($row->action,'_'.$row->action),$row->from,$row->to)?></li>
					<?php } ?>
				</ul>
				<?php } ?>
				<?php if($change['comment'] != '') { ?>
				<div class="change_comment">
					<?php echo formattext($change['comment'],true)?>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		
		<?php if($user->group['update_tickets'] OR $user->group['comment_tickets']) { ?>
		<form action="<?php echo $uri->geturi()?>" method="post">
		<input type="hidden" name="update" value="1" />
		<h2><?php echo l('update_ticket')?></h2>
		<div id="update_ticket">
			<?php if(count($errors)) { ?>
			<div class="message error">
				<?php foreach($errors as $error) { ?>
				<?php echo $error?><br />
				<?php } ?>
			</div>
			<?php } ?>
			<?php if($user->group['comment_tickets']) { ?>
			<textarea name="comment"></textarea>
			<?php } ?>
			<?php if($user->group['update_tickets']) { ?>
			<fieldset class="properties">
				<legend><?php echo l('ticket_properties')?></legend>
				<div class="properties">
					<div class="property <?php echo altbg() ?>">
						<span><?php echo l('type')?></span>
						<select name="type">
							<?php foreach(ticket_types() as $type) { ?>
							<option value="<?php echo $type['id']?>"<?php echo iif($type['id']==$ticket['type'],' selected="selected"')?>><?php echo $type['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg() ?>">
						<span><?php echo l('assigned_to')?></span>
						<select name="assign_to">
							<option value="0"></option>
							<?php foreach(project_managers() as $manager) { ?>
							<option value="<?php echo $manager['id']?>"<?php echo iif($manager['id']==$ticket['assigned_to'],' selected="selected"')?>><?php echo $manager['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg() ?>">
						<span><?php echo l('priority')?></span>
						<select name="priority">
							<?php foreach(ticket_priorities() as $priority) { ?>
							<option value="<?php echo $priority['id']?>"<?php echo iif($priority['id']==$ticket['priority'],' selected="selected"')?>><?php echo $priority['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg() ?>">
						<span><?php echo l('severity')?></span>
						<select name="severity">
							<?php foreach(ticket_severities() as $severity) { ?>
							<option value="<?php echo $severity['id']?>"<?php echo iif($severity['id']==$ticket['severity'],' selected="selected"')?>><?php echo $severity['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg() ?>">
						<span><?php echo l('milestone')?></span>
						<select name="milestone">
							<?php foreach(project_milestones() as $milestone) { ?>
							<?php if(!$milestone['locked'] or ($milestone['locked'] && $ticket['milestone_id'] == $milestone['id'])) { ?>
							<option value="<?php echo $milestone['id']?>"<?php echo iif($milestone['id']==$ticket['milestone_id'],' selected="selected"')?>><?php echo $milestone['milestone']?></option>
							<?php } ?>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg() ?>">
						<span><?php echo l('version')?></span>
						<select name="version">
							<option value="0"></option>
							<?php foreach(project_versions() as $version) { ?>
							<option value="<?php echo $version['id']?>"<?php echo iif($version['id']==$ticket['version_id'],' selected="selected"')?>><?php echo $version['version']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg() ?>">
						<span><?php echo l('component')?></span>
						<select name="component">
							<option value="0"><?php echo l('none')?></option>
							<?php foreach(project_components() as $component) { ?>
							<option value="<?php echo $component['id']?>"<?php echo iif($component['id']==$ticket['component_id'],' selected="selected"')?>><?php echo $component['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg() ?>">
						<span><?php echo l('summary')?></span>
						<input type="text" name="summary" value="<?php echo $ticket['summary']?>" />
					</div>
					<?php ($hook = FishHook::hook('template_update_ticket_properties')) ? eval($hook) : false; ?>
				</div>
				<div class="clear"></div>
				<div class="properties">
					<div class="property odd">
						<span><?php echo l('action')?></span>
						<table>
							<tr>
								<td>
						<?php if(!$ticket['closed']) { ?>
							<input type="radio" name="action" value="mark" checked="checked" id="mark" /> <label for="mark"><?php echo l('mark_as')?></label> <select name="mark_as">
								<?php foreach(ticket_status_list() as $status) { ?>
									<option value="<?php echo $status['id']?>"<?php echo iif($status['id']==$ticket['status'],' selected="selected"')?>><?php echo $status['name']?></option>
								<?php } ?>
							</select>
							<br />
							<input type="radio" name="action" value="close" id="close" /> <label for="close"><?php echo l('close_as')?></label> <select name="close_as">
								<?php foreach(ticket_status_list(0) as $status) { ?>
									<option value="<?php echo $status['id']?>"<?php echo iif($status['id']==$ticket['status'],' selected="selected"')?>><?php echo $status['name']?></option>
								<?php } ?>
							</select>
							<?php } else if($ticket['closed']) { ?>
							<input type="radio" name="action" value="reopen" id="reopen" /> <label for="reopn"><?php echo l('reopen_as')?></label> <select name="reopen_as">
								<?php foreach(ticket_status_list() as $status) { ?>
									<option value="<?php echo $status['id']?>"<?php echo iif($status['id']==$ticket['status'],' selected="selected"')?>><?php echo $status['name']?></option>
								<?php } ?>
							</select>
							<?php } ?>
								</td>
							</tr>
						</table>
					</div>
					<div class="property even">
						<span><?php echo l('private_ticket')?></span>
						<input type="checkbox" name="private" id="private" value="1"<?php echo iif($ticket['private'],' checked="checked"')?> />
					</div>
					<?php if(!$user->loggedin) { ?>
					<div class="property">
						<span><?php echo l('your_name')?></span>
						<input type="text" name="name" value="<?php echo $_COOKIE['guestname']?>" />
					</div>
					<?php } ?>
				</div>
				<div class="clear"></div>
			</fieldset>
			<?php } ?>
			<?php if(!$user->loggedin and settings('recaptcha_enabled')) { ?>
			<fieldset>
				<legend><?php echo l('recaptcha')?></legend>
				<?php echo recaptcha_get_html(settings('recaptcha_pubkey'), $recaptcha_error)?>
			</fieldset>
			<?php } ?>
			<input type="submit" value="<?php echo l('update')?>" />
		</div>
		</form>
		<?php } ?>
		<?php require(template('footer')); ?>
	</body>
</html>