<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=$project['name']?> / <?=l('milestone_x',$milestone['milestone'])?></title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<? require(template('breadcrumbs')); ?>
		
		<h1><?=l('milestone')?>: <em><?=$milestone['milestone']?><?=(!empty($milestone['codename']) ? ' <small>"'.$milestone['codename'].'"</small>' : '')?></em></h1>
		<div class="milestone">
			<div class="info">
				<p class="date">
					<? if($milestone['due'] == 0 && $milestone['completed'] == 0) { ?>
					
					<? } elseif($milestone['completed'] > 0) { ?>
					<?=l('completed_on_x',date("d/m/Y",$project['completed']))?>
					<? } elseif($milestone['due'] <= time()) { ?>
					<?=l('due_x_ago',timesince($milestone['due'],true))?>
					<? } elseif($milestone['due'] > time()) { ?>
					<?=l('due_x_from_now',timefrom($milestone['due'],true))?>
					<? } ?>
				</p>
				<table>
					<tr>
						<td>
							<table class="progress" cellspacing="0">
								<tr>
									<? if($milestone['tickets']['percent']['closed'] != 0) { ?><td class="closed" width="<?=$milestone['tickets']['percent']['closed']?>%"><a href="<?=$uri->anchor($project['slug'],'tickets')?>?milestone=<?=$milestone['milestone']?>&status=closed"></a></td><? } ?>
									<? if($milestone['tickets']['percent']['open'] != 0) { ?><td class="open" width="<?=$milestone['tickets']['percent']['open']?>%"><a href="<?=$uri->anchor($project['slug'],'tickets')?>?milestone=<?=$milestone['milestone']?>&status=open"></a></td><? } ?>
								</tr>
							</table>
						</td>
						<td class="percent"><?=$milestone['tickets']['percent']['closed']?>%</td>
					</tr>
				</table>
				<dl>
					<dt><?=l('closed_tickets')?>:</dt>
					<dd><a href="<?=$uri->anchor($project['slug'],'tickets')?>?milestone=<?=$milestone['milestone']?>&status=closed"><?=$milestone['tickets']['closed']?></a></dd>
					<dt><?=l('active_tickets')?>:</dt>
					<dd><a href="<?=$uri->anchor($project['slug'],'tickets')?>?milestone=<?=$milestone['milestone']?>&status=open"><?=$milestone['tickets']['open']?></a></dd>
					<dt><?=l('total_tickets')?>:</dt>
					<dd><a href="<?=$uri->anchor($project['slug'],'tickets')?>?milestone=<?=$milestone['milestone']?>"><?=$milestone['tickets']['total']?></a></dd>
				</dl>
				<?=$milestone['info']?>
			</div>
		</div>
		
		<? require(template('footer')); ?>
	</body>
</html>