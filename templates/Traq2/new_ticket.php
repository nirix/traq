<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo $project['name']?> / <?php echo l('new_ticket')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<?php require(template('breadcrumbs')); ?>
		
		<h1><?php echo l('new_ticket')?></h1>
		
		<form action="<?php echo $uri->geturi()?>" method="post" class="new_ticket">
		<?php if(isset($errors)) { ?>
		<div class="message error">
			<?php foreach($errors as $error) { ?>
			<?php echo $error?><br />
			<?php } ?>
		</div>
		<?php } ?>
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td width="140">
						<fieldset>
							<legend><?php echo l('type'); ?></legend>
							<select name="type" id="ticket_type">
								<?php foreach(ticket_types() as $type) { ?>
								<option value="<?php echo $type['id']?>"><?php echo $type['name']?></option>
								<?php } ?>
							</select>
						</fieldset>
					</td>
					<td width="5"></td>
					<td>
						<fieldset<?php echo (isset($errors['summary']) ? ' class="error"' : '')?>>
							<legend><?php echo l('summary')?></legend>
							<input type="text" name="summary" class="summary" value="<?php echo $_POST['summary']?>" />
						</fieldset>
					</td>
				</tr>
			</table>
			
			<fieldset<?php echo (isset($errors['body']) ? ' class="error"' : '')?>>
				<legend><?php echo l('description')?></legend>
				<textarea name="body" class="body" id="ticket_body"><?php echo $_POST['body']?></textarea>
			</fieldset>
			
			<fieldset id="ticket_properties" class="new_ticket properties">
				<legend><?php echo l('properties')?></legend>
				<div class="properties">
					<div class="property <?php echo altbg()?>">
						<span><?php echo l('assigned_to')?></span>
						<select name="assign_to">
							<option value="0"></option>
							<?php foreach(project_managers() as $manager) { ?>
							<option value="<?php echo $manager['id']?>"><?php echo $manager['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg()?>">
						<span><?php echo l('priority')?></span>
						<select name="priority">
							<?php foreach(ticket_priorities() as $priority) { ?>
							<option value="<?php echo $priority['id']?>"<?php echo ($priority['id']=='3' ? ' selected="selected"' : '')?>><?php echo $priority['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg()?>">
						<span><?php echo l('severity')?></span>
						<select name="severity">
							<?php foreach(ticket_severities() as $severity) { ?>
							<option value="<?php echo $severity['id']?>"<?php echo $severity['id']?>"<?php echo ($severity['id']=='4' ? ' selected="selected"' : '')?>><?php echo $severity['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg()?>">
						<span><?php echo l('milestone')?></span>
						<select name="milestone">
							<?php foreach(project_milestones() as $milestone) { ?>
							<?php if(!$milestone['locked']) { ?>
							<option value="<?php echo $milestone['id']?>"><?php echo $milestone['milestone']?></option>
							<?php } ?>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg()?>">
						<span><?php echo l('version')?></span>
						<select name="version">
							<option value="0"></option>
							<?php foreach(project_versions() as $version) { ?>
							<option value="<?php echo $version['id']?>"><?php echo $version['version']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg()?>">
						<span><?php echo l('component')?></span>
						<select name="component">
							<option value="0"><?php echo l('none')?></option>
							<?php foreach(project_components() as $component) { ?>
							<option value="<?php echo $component['id']?>"><?php echo $component['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="property <?php echo altbg()?>">
						<span><?php echo l('private_ticket')?></span>
						<input type="checkbox" name="private" value="1" />
					</div>
				</div>
				<div class="clear"></div>
			</fieldset>
			
			<?php if(!$user->loggedin) { ?>
			<fieldset<?php echo (isset($errors['name']) ? ' class="error"' : '')?>>
				<legend><?php echo l('your_name')?></legend>
				<input type="text" name="name" value="<?php echo $_COOKIE['guestname']?>" />
			</fieldset>
			<?php if(settings('recaptcha_enabled')) { ?>
			<fieldset<?php echo (isset($errors['key']) ? ' class="error"' : '')?>>
				<legend><?php echo l('recaptcha')?></legend>
				<?php echo recaptcha_get_html(settings('recaptcha_pubkey'), $recaptcha_error)?>
			</fieldset>
			<?php } ?>
			<?php } ?>
			
			<p id="buttons">
				<input type="submit" value="Create Ticket" />
			</p>
		</form>
		<script type="text/javascript">
			getTicketTemplate()
		</script>
		<?php require(template('footer')); ?>
	</body>
</html>