<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo settings('title')?> / <?php echo l('usercp')?></title>
		<?php require(template('headerinc')); ?>
	</head>
	<body>
		<?php require(template('header')); ?>
		
		<h1><?php echo l('usercp')?></h1>
		
		<table width="100%" cellpadding="0" cellspacing="5" class="form">
			<tr>
				<td width="50%">
					<fieldset>
						<legend><?php echo l('tickets')?></legend>
						<p>
							<span><?php echo l('created')?></span>
							<?php echo $tickets['opened']?>
						</p>
						<p>
							<span><?php echo l('updates')?></span>
							<?php echo $tickets['updates']?>
						</p>
						<h4><?php echo l('assigned_tickets_x',count($tickets['assigned']))?></h4>
						<?php foreach($tickets['assigned'] as $ticket) { ?>
						<div>
							<a href="<?php echo $uri->anchor($ticket['project']['slug'],'ticket-'.$ticket['ticket_id'])?>"><?php echo $ticket['summary']?> <small>(<?php echo l('ticket_x',$ticket['ticket_id'])?>)</small></a>
						</div>
						<?php } ?>
					</fieldset>
				</td>
				<td>
					<form action="<?php echo $uri->geturi()?>" method="post">
						<input type="hidden" name="action" value="save" />
						<?php if(count($errors)) { ?>
						<div class="message error">
							<?php echo implode('<br />',$errors)?>
						</div>
						<?php } ?>
						<fieldset>
							<legend><?php echo l('settings')?></legend>
							<p>
								<span><?php echo l('email')?></span>
								<input type="text" name="email" value="<?php echo $user->info['email']?>" />
							</p>
						
							<p>
								<span><?php echo l('password')?></span>
								<input type="password" name="password" />
							</p>
							<p>
								<span><?php echo l('new_password')?></span>
								<input type="password" name="new_password" />
							</p>
							<p>
								<span><?php echo l('new_password')?></span>
								<input type="password" name="new_password_confirm" /> <small><?php echo l('confirm')?></small>
							</p>
							<p>
								<span>&nbsp;</span>
								<input type="submit" value="<?php echo l('update')?>" />
							</p>
						</fieldset>
					</form>
				</td>
			</tr>
		</table>
		
		<?php require(template('footer')); ?>
	</body>
</html>