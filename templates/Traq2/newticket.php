<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=$project['name']?> / <?=l('new_ticket')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<? require(template('breadcrumbs')); ?>
		
		<h1><?=l('new_ticket')?></h1>
		
		<form action="" method="post" class="new_ticket">
			<fieldset>
				<legend><?=l('summary')?></legend>
				<input type="text" name="summary" class="summary" />
			</fieldset>
			
			<fieldset>
				<legend><?=l('description')?></legend>
				<textarea name="body" class="body"></textarea>
			</fieldset>
			
			<fieldset id="ticket_properties">
				<legend><?=l('properties')?></legend>
				<table width="100%" cellpading="0" cellspacing="0">
					<tr>
						<th class="col1"><?=l('type')?></th>
						<td>
							<select name="type">
								<? foreach(ticket_types() as $type) { ?>
								<option value="<?=$type['name']?>"><?=$type['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('assign_to')?></th>
						<td>
							<select name="assign_to">
								<option value="" selected=""></option>
								<? foreach(project_managers() as $manager) { ?>
								<option value="<?=$manager['id']?>"><?=$manager['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('priority')?></th>
						<td>
							<select name="priority">
								<? foreach(ticket_priorities() as $priority) { ?>
								<option value="<?=$priority['name']?>"<?=($priority['name']=='Normal' ? ' selected="selected"' : '')?>><?=$priority['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('severity')?></th>
						<td>
							<select name="severity">
								<? foreach(ticket_severities() as $severity) { ?>
								<option value="<?=$severity['name']?>"<?=($severity['name']=='Normal' ? ' selected="selected"' : '')?>><?=$severity['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('milestone')?></th>
						<td>
							<select name="milestone">
								<? foreach(project_milestones() as $milestone) { ?>
								<option value="<?=$milestone['id']?>"><?=$milestone['milestone']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('version')?></th>
						<td>
							<select name="version">
								<option value="" selected=""></option>
								<? foreach(project_versions() as $version) { ?>
								<option value="<?=$version['id']?>"><?=$version['version']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('component')?></th>
						<td>
							<select name="component">
								<? foreach(project_components() as $component) { ?>
								<option value="<?=$component['id']?>"<?=iif($component['default'],' selected="selected"')?>><?=$component['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('private_ticket')?></th>
						<td><input type="checkbox" name="private" /></td>
					</tr>
				</table>
			</fieldset>
			
			<? if(!$user->loggedin) { ?>
			<fieldset<?=(isset($errors['name']) ? ' class="error"' : '')?>>
				<legend><?=l('your_name')?></legend>
				<input type="text" name="name" value="<?=$_COOKIE['guestname']?>" />
			</fieldset>
			<? if(settings('use_recaptcha')) { ?>
			<fieldset<?=(isset($errors['key']) ? ' class="error"' : '')?>>
				<legend><?=l('recaptcha')?></legend>
				<? echo recaptcha_get_html(settings('recaptcha_pubkey'), $error); ?>
			</fieldset>
			<? } ?>
			<? } ?>
			
			<p id="buttons">
				<input type="submit" value="Create Ticket" />
			</p>
		</form>
		
		<? require(template('footer')); ?>
	</body>
</html>