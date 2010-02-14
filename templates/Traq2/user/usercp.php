<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=l('usercp')?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<h1><?=l('usercp')?></h1>
		
		<table width="100%" cellpadding="0" cellspacing="5" class="form">
			<tr>
				<td width="50%">
					<fieldset>
						<legend><?=l('tickets')?></legend>
						<p>
							<span><?=l('created')?></span>
							<?=$tickets['opened']?>
						</p>
						<p>
							<span><?=l('updates')?></span>
							<?=$tickets['updates']?>
						</p>
						<h4><?=l('assigned_tickets_x',count($tickets['assigned']))?></h4>
						<? foreach($tickets['assigned'] as $ticket) { ?>
						<div>
							<a href="<?=$uri->anchor($ticket['project']['slug'],'ticket-'.$ticket['ticket_id'])?>"><?=$ticket['summary']?> <small>(<?=l('ticket_x',$ticket['ticket_id'])?>)</small></a>
						</div>
						<? } ?>
					</fieldset>
				</td>
				<td>
					<form action="<?=$uri->geturi()?>" method="post">
						<input type="hidden" name="action" value="save" />
						<? if(count($errors)) { ?>
						<div class="message error">
							<?=implode('<br />',$errors)?>
						</div>
						<? } ?>
						<fieldset>
							<legend><?=l('settings')?></legend>
							<p>
								<span><?=l('email')?></span>
								<input type="text" name="email" value="<?=$user->info['email']?>" />
							</p>
						
							<p>
								<span><?=l('password')?></span>
								<input type="password" name="password" />
							</p>
							<p>
								<span><?=l('new_password')?></span>
								<input type="password" name="new_password" />
							</p>
							<p>
								<span><?=l('new_password')?></span>
								<input type="password" name="new_password_confirm" /> <small><?=l('confirm')?></small>
							</p>
							<p>
								<span>&nbsp;</span>
								<input type="submit" value="<?=l('update')?>" />
							</p>
						</fieldset>
					</form>
				</td>
			</tr>
		</table>
		
		<? require(template('footer')); ?>
	</body>
</html>